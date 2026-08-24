#!/bin/sh
set -e
cd "$(dirname "$0")/.."
SITE_ROOT="$(pwd)"
PROD_PATH=/var/www/oftalmag_ru_usr/data/www/oftalmag.ru
HOST=almamed
PROD_USER=oftalmag_ru_usr
REMOTE=origin
BRANCH=main

LOCAL_REV="$(git rev-parse HEAD)"
LOCAL_SUBJECT="$(git log -1 --format='%s')"

echo "==> Local: $LOCAL_REV $LOCAL_SUBJECT"
echo "==> Push local commits (if any)"
git status -sb | head -5
git push "$REMOTE" "$BRANCH"

PUSHED_REV="$(git rev-parse HEAD)"
if [ "$LOCAL_REV" != "$PUSHED_REV" ]; then
  echo "ERROR: HEAD changed during push ($LOCAL_REV -> $PUSHED_REV)" >&2
  exit 1
fi

echo "==> Deploy on production via git (expect $PUSHED_REV)"
ssh "$HOST" "cd $PROD_PATH &&
  set -e
  if [ -f bitrix/license_key.php ]; then
    cp -a bitrix/license_key.php /tmp/oftalmag_license_key.php.bak
  fi
  if [ ! -d .git ]; then
    git init
    git checkout -B $BRANCH 2>/dev/null || git checkout -b $BRANCH
    git remote add $REMOTE https://github.com/bziksv/oftalmag.git
  else
    git remote set-url $REMOTE https://github.com/bziksv/oftalmag.git 2>/dev/null || true
  fi
  GIT_TERMINAL_PROMPT=0 git fetch $REMOTE $BRANCH
  DEPLOY_REV=\$(git rev-parse FETCH_HEAD)
  if [ \"\$DEPLOY_REV\" != \"$PUSHED_REV\" ]; then
    echo \"ERROR: prod FETCH_HEAD=\$DEPLOY_REV, expected $PUSHED_REV\" >&2
    exit 1
  fi
  GIT_TERMINAL_PROMPT=0 git checkout FETCH_HEAD -- .
  echo \"\$DEPLOY_REV\" > .deploy-revision
  if [ ! -f bitrix/license_key.php ] && [ -f /tmp/oftalmag_license_key.php.bak ]; then
    cp -a /tmp/oftalmag_license_key.php.bak bitrix/license_key.php
    echo 'restored bitrix/license_key.php'
  fi
  chown -R ${PROD_USER}:${PROD_USER} .
  rm -rf bitrix/cache/* bitrix/managed_cache/* bitrix/stack_cache/* bitrix/html_pages/oftalmag.ru/* 2>/dev/null || true
  echo 0 > bitrix/html_pages/.size 2>/dev/null || true
  git log -1 --oneline FETCH_HEAD
  echo cache_cleared
  echo deploy_revision=\$DEPLOY_REV
"

PROD_REV="$(ssh "$HOST" "cat $PROD_PATH/.deploy-revision 2>/dev/null" || true)"
if [ "$PROD_REV" != "$PUSHED_REV" ]; then
  echo "ERROR: prod revision mismatch (prod=$PROD_REV, git=$PUSHED_REV)" >&2
  exit 1
fi

echo "==> Verified: prod == git ($PUSHED_REV)"

echo "==> Verify"
/usr/bin/curl -sS -o /dev/null -w 'home %{http_code}\n' --max-time 20 https://oftalmag.ru/
/usr/bin/curl -sS -o /dev/null -w 'orders %{http_code}\n' --max-time 20 https://oftalmag.ru/personal/orders/
echo "==> Done"
