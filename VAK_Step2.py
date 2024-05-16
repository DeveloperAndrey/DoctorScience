import sys
import mysql.connector    
from selenium_stealth import stealth
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException
from selenium.webdriver.common.keys import Keys 

from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service as ChromiumService
from webdriver_manager.chrome import ChromeDriverManager

from selenium.webdriver.edge.service import Service as EdgeService
from webdriver_manager.microsoft import EdgeChromiumDriverManager

from selenium.webdriver.firefox.service import Service as FirefoxService
from webdriver_manager.firefox import GeckoDriverManager

from time import sleep, time
import datetime
import math

sys.stdout.reconfigure(encoding='utf-8')
# Параметры подключения к базе данных
config = {
    'user': 'admin_doctors',
    'password': 'Lg8y{:P#(]RPieFM',
    'host': 'localhost',
    'database': 'admin_doctors',
    'raise_on_warnings': True
}

# Настройки браузера

options = Options()
options.add_argument("--window-size=1920,1080")
options.add_argument('--no-sandbox')
options.add_argument("--disable-extensions")
options.add_experimental_option("excludeSwitches", ["enable-automation"])
options.add_experimental_option("useAutomationExtension", False)
options.add_argument("--disable-blink-features=AutomationControlled")
browser = webdriver.Chrome(service=ChromiumService(ChromeDriverManager().install()), options=options)
browser.implicitly_wait(6)

# Apply stealth options
stealth(browser,
        user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        languages=["ru-RU", "ru"],
        vendor="Google Inc.",
        platform="Win32",
        webgl_vendor="Intel Inc.",
        renderer="Intel Iris OpenGL Engine",
        fix_hairline=True,
        )

# Подключение к базе данных
try:
    conn = mysql.connector.connect(**config)
    print("Успешное подключение к базе данных")
except mysql.connector.Error as err:
    print(f"Ошибка подключения к базе данных: {err}")

if conn:
    # Создание курсора для выполнения запросов
    cursor = conn.cursor()

    # Пример заполнения данных
    try:
        # Определение количества записей
        cursor.execute("SELECT COUNT(*) FROM vak_step1")
        total_rows = cursor.fetchone()[0]
        # Задание параметров для пагинации
        page_size = 100
        num_pages = (total_rows + page_size - 1) / page_size  # Вычисление количества страниц

        # Перебор всех страниц
        for page in range(math.ceil(num_pages)):
            # Выполнение SQL запроса для текущей страницы
            cursor.execute("SELECT `href` FROM `vak_step1` LIMIT %s OFFSET %s", (page_size, page * page_size))
            records = cursor.fetchall()
            # Обработка записей
            data_list = []
            for record in records:
                # Здесь вы можете обрабатывать каждую запись, например, добавлять их в список
                browser.get(record[0])
                col0 = record[0]
                col1 = browser.find_element(By.XPATH,'//td[contains(text(),\'Шифр научной специальности\')]/../td[2]').text
                col2 = browser.find_element(By.XPATH,'//td[contains(text(),\'Наименование организации место защиты\')]/../td[2]').text
                col3 = browser.find_element(By.XPATH,'//td[contains(text(),\'Автореферат\')]/../td[2]/a').get_attribute("href")
                col4 = browser.find_element(By.XPATH,'//td[contains(text(),\'Тип диссертации\')]/../td[2]').text
                col5 = browser.find_element(By.XPATH,'//td[contains(text(),\'Отрасль науки\')]/../td[2]').text
                element_1 = browser.find_elements(By.XPATH,'//tr[last()]//font')
                element_2 = browser.find_elements(By.XPATH,'//tr[last()]//p')
                col6 = element_1[0].text if element_1 else "Нет данных"
                col7 = element_2[0].text if element_2 else "Нет данных"
                # Создаем словарь с данными из текущей строки
                row_data = {
                    "Ссылка": col0,
                    "Шифр научной специальности": col1,
                    "Наименование организации место защиты": col2,
                    "Автореферат": col3,
                    "Тип диссертации": col4,
                    "Отрасль науки": col5,
                    "Решение": col6,
                    "Приказ": col7
                }
                # Добавляем словарь в список
                data_list.append(row_data)
            sql = """
                INSERT INTO `vak_step2` (`href`, `cipher`, `protection`, `abstract`, `type`, `industry`, `solution`, `decree`, `data_update`) 
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s) 
                ON DUPLICATE KEY UPDATE 
                `cipher` = VALUES(`cipher`), 
                `protection` = VALUES(`protection`),
                `abstract` = VALUES(`abstract`),
                `type` = VALUES(`type`),
                `industry` = VALUES(`industry`),
                `solution` = VALUES(`solution`),
                `decree` = VALUES(`decree`)
                `data_update` = VALUES(`data_update`)
            """
            val = [(item["Ссылка"], item["Шифр научной специальности"], item["Наименование организации место защиты"], item["Автореферат"],item["Тип диссертации"],item["Отрасль науки"],item["Решение"],item["Приказ"], datetime.datetime.now()) for item in data_list]
            cursor.executemany(sql, val)
            conn.commit()
            print("Данные успешно добавлены в таблицу")

    except mysql.connector.Error as err:
        print(f"Ошибка при добавлении данных: {err}")
    finally:
	    # Закрытие курсора и соединения
	    cursor.close()
	    conn.close()