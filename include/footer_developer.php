<!--
<a href="https://prime-ltd.su/" style="text-align: left; color: white; font-weight: bold; font-size: 12px;"><img width="27" alt="small-logo.png" src="/upload/medialibrary/c5c/u8ce14wmjwocpd697v26pqi1cn5g5kpc.png" height="27" title="prime-ltd" style="float: left; margin-right:4px;">ИНТЕРНЕТ-МАРКЕТИНГ<br>
 ПРОДВИЖЕНИЕ САЙТОВ</a>
-->
<div style="text-align: center;display: flex; align-items: center; ">
 <a href="https://prime-ltd.su/"> <img alt="Продвижение сайтов" src="https://prime-ltd.su/logo/white.svg" style="width: 75% !important; height: auto !important; margin-left: 45px;" title="Продвижение сайтов"> </a>
</div>
 <br>
 <br>
 <br>
<style>
 /* Стили для спойлера */
.footer-partner-spoiler {
  margin: 14px 0;
}

.partner-spoiler-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 0;
  cursor: pointer;
  color: #ffffff;
  font-weight: 600;
  font-size: 10px;
  list-style: none;
  background: transparent;
  border: none;
  border-radius: 0;
  transition: color 0.3s ease;
}

.partner-spoiler-header:hover {
  color: #4dabf7;
}

.partner-spoiler-header::after {
  content: '+';
  font-size: 11px;
  font-weight: normal;
  transition: transform 0.3s ease;
  color: #ffffff;
  margin-left: 8px;
}

.partner-spoiler-header:hover::after {
  color: #4dabf7;
}

.footer-partner-spoiler[open] .partner-spoiler-header::after {
  content: '−';
}

.partner-spoiler-title {
  color: #ffffff;
}

/* Содержимое спойлера */
.footer-partner-block {
  background: transparent;
  padding: 10px 0 0 0;
}

.partner-intro {
  margin-bottom: 14px;
  text-align: center;
}

.partner-intro p {
  color: #cccccc;
  font-size: 10px;
  line-height: 1.4;
  max-width: 600px;
  margin: 0 auto;
}

/* Сетка категорий */
.footer-medical-categories {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 12px;
  margin-bottom: 14px;
}

.medical-category {
  position: relative;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 5px;
  padding: 12px;
  transition: all 0.3s ease;
}

.medical-category:hover {
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  border-color: #0056b3;
}

.category-header {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 8px;
  cursor: pointer;
}

.category-title {
  font-weight: 600;
  color: #ffffff;
  font-size: 10px;
}

.category-hint {
  font-size: 8px;
  color: #888;
  font-style: italic;
}

/* Выпадающие подкатегории */
.subcategories-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #1a1a1a;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0 0 5px 5px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  padding: 12px;
  z-index: 100;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px);
  transition: all 0.3s ease;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.medical-category:hover .subcategories-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.subcategory-column {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.subcategory {
  margin-bottom: 6px;
}

.subcategory-title {
  font-weight: 500;
  font-size: 8px;
  color: #4dabf7;
  margin-bottom: 4px;
  padding-bottom: 1px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.subcategory-items {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.subcategory-items span {
  font-size: 8px;
  color: #cccccc;
  padding: 1px 0;
  transition: color 0.2s ease;
}

.subcategory-items span:hover {
  color: #4dabf7;
}

/* Призыв к действию */
.partner-cta {
  text-align: center;
  padding-top: 12px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.partner-cta p {
  color: #ffffff;
  margin-bottom: 8px;
  font-size: 10px;
}

.consult-button {
  background: #0056b3;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 3px;
  font-size: 9px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.consult-button:hover {
  background: #004494;
}

/* Адаптивность для мобильных */
@media (max-width: 768px) {
  .footer-partner-block {
    padding: 8px 0 0 0;
  }
  
  .footer-medical-categories {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  
  .subcategories-dropdown {
    position: static;
    opacity: 1;
    visibility: visible;
    transform: none;
    box-shadow: none;
    border: none;
    padding: 8px 0 0 0;
    display: block;
  }
  
  .category-hint {
    display: none;
  }
  
  .medical-category {
    margin-bottom: 8px;
    padding: 10px;
  }
}

@media (max-width: 480px) {
  .partner-spoiler-header {
    font-size: 9px;
    padding: 4px 0;
  }
  
  .partner-intro p {
    font-size: 9px;
  }
  
  .category-title {
    font-size: 9px;
  }
  
  .footer-medical-categories {
    gap: 8px;
  }
}
</style> <details class="footer-partner-spoiler"> <summary class="partner-spoiler-header"><span class="partner-spoiler-title">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Сотрудничество с партнерами</span> </summary>
<div class="footer-partner-block">
	<div class="partner-intro">
		<p>
			 Мы специализируемся на офтальмологическом оборудовании, но тесно сотрудничаем с проверенными поставщиками оборудования для других медицинских направлений. Можем помочь с подбором и предоставить контакты надежных партнеров.
		</p>
	</div>
	<div class="footer-medical-categories">
		 <!-- Диагностическое и лабораторное оборудование -->
		<div class="medical-category">
			<div class="category-header">
 <span class="category-title">Диагностическое и лабораторное оборудование</span>
			</div>
			<div class="subcategories-dropdown">
				<div class="subcategory-column">
					<div class="subcategory">
						<div class="subcategory-title">
							 Визуализация
						</div>
						<div class="subcategory-items">
							 УЗИ аппараты Рентгеновские аппараты Томографы Маммографы
						</div>
					</div>
					<div class="subcategory">
						<div class="subcategory-title">
							 Лаборатория
						</div>
						<div class="subcategory-items">
							 Анализаторы Центрифуги Микроскопы Термостаты
						</div>
					</div>
				</div>
				<div class="subcategory-column">
					<div class="subcategory">
						<div class="subcategory-title">
							 Функциональная диагностика
						</div>
						<div class="subcategory-items">
							 ЭКГ аппараты Спирометры Холтеровские мониторы Пульсоксиметры
						</div>
					</div>
					<div class="subcategory">
						<div class="subcategory-title">
							 Эндоскопия
						</div>
						<div class="subcategory-items">
							 Гастроскопы Колоноскопы Бронхоскопы Цистоскопы
						</div>
					</div>
				</div>
			</div>
		</div>
		 <!-- Лечебное и реабилитационное оборудование -->
		<div class="medical-category">
			<div class="category-header">
 <span class="category-title">Лечебное и реабилитационное оборудование</span>
			</div>
			<div class="subcategories-dropdown">
				<div class="subcategory-column">
					<div class="subcategory">
						<div class="subcategory-title">
							 Анестезиология и реанимация
						</div>
						<div class="subcategory-items">
							 Аппараты ИВЛ Дефибрилляторы Наркозно-дыхательные аппараты Мониторы пациента
						</div>
					</div>
					<div class="subcategory">
						<div class="subcategory-title">
							 Хирургия
						</div>
						<div class="subcategory-items">
							 Электрохирургические аппараты Лазерные аппараты Сшивающие аппараты Аспираторы
						</div>
					</div>
				</div>
				<div class="subcategory-column">
					<div class="subcategory">
						<div class="subcategory-title">
							 Терапия
						</div>
						<div class="subcategory-items">
							 Физиотерапия Инфузионные насосы Кислородные концентраторы Ларингоскопы
						</div>
					</div>
					<div class="subcategory">
						<div class="subcategory-title">
							 Реабилитация
						</div>
						<div class="subcategory-items">
							 Тренажеры Массажные кушетки Ортопедические изделия Инвалидные коляски
						</div>
					</div>
				</div>
			</div>
		</div>
		 <!-- Специализированное и вспомогательное оборудование -->
		<div class="medical-category">
			<div class="category-header">
 <span class="category-title">Специализированное и вспомогательное оборудование</span>
			</div>
			<div class="subcategories-dropdown">
				<div class="subcategory-column">
					<div class="subcategory">
						<div class="subcategory-title">
							 Специализированные отделения
						</div>
						<div class="subcategory-items">
							 Гинекология Урология ЛОР оборудование Дерматология
						</div>
					</div>
					<div class="subcategory">
						<div class="subcategory-title">
							 Стоматология
						</div>
						<div class="subcategory-items">
							 Стоматологические установки Стоматологические микроскопы Скалеры Автоклавы
						</div>
					</div>
				</div>
				<div class="subcategory-column">
					<div class="subcategory">
						<div class="subcategory-title">
							 Вспомогательное оборудование
						</div>
						<div class="subcategory-items">
							 Медицинская мебель Стерилизация Расходные материалы Аптечки и укладки
						</div>
					</div>
					<div class="subcategory">
						<div class="subcategory-title">
							 Другие направления
						</div>
						<div class="subcategory-items">
							 Ветеринария Неонатология Ортопедия Травматология
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="partner-cta">
		<p>
			 Нужно оборудование других направлений? <strong>Поможем найти надежных поставщиков!</strong>
		</p>
 <button class="consult-button">Получить консультацию по партнерам</button>
	</div>
</div>
 </details>