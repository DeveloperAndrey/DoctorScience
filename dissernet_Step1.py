import sys
import mysql.connector    
from selenium import webdriver
from selenium_stealth import stealth

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
import re
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


options = Options()
options.add_argument("--window-size=1920,1080")
options.add_argument('--no-sandbox')
options.add_argument("--disable-extensions")
options.add_experimental_option("excludeSwitches", ["enable-automation"])
options.add_experimental_option("useAutomationExtension", False)
options.add_argument("--disable-blink-features=AutomationControlled")

browser = webdriver.Chrome(service=ChromiumService(ChromeDriverManager().install()), options=options)
browser.implicitly_wait(5)

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
        count = 100
        k = 0
        browser.get(f'https://dissernet.org/person?profile=false&main%5BcurrentPage%5D=1&main%5BperPage%5D=1')
        count_person = browser.find_element(By.XPATH,'//div[@class="t-table__header-counter"]').text
        count_person = re.search(r'\d+', count_person).group()
        sql = "UPDATE statistics SET must=%s, data_update=%s WHERE id = 3"
        val = (count_person,datetime.datetime.now()) 
        cursor.execute(sql, val)
        # Подтверждение изменений в базе данных
        conn.commit()

        for i in range(k, math.ceil(int(count_person)/count)):
            browser.get(f'https://dissernet.org/person?profile=false&main%5BcurrentPage%5D={i}&main%5BperPage%5D={count}')
            sleep(1)
            browser.find_element(By.XPATH,'//a[@class="t-table__item"]').click()
            table = browser.find_elements(By.XPATH,'//div[@class="t-unit-materials -personTable"]/div')
            data_list = []
            for tr in table[1:]:
                col1 = tr.find_element(By.XPATH,'./div[@class="col-1"]/a').text
                col2 = tr.find_element(By.XPATH,'./div[@class="col-1"]/a').get_attribute("href")
                col3 = tr.find_element(By.XPATH,'./div[@class="col-2"]').text
                col4 = tr.find_element(By.XPATH,'./div[@class="col-3"]').text
                col5 = tr.find_element(By.XPATH,'./div[@class="col-4"]').text
                col6 = tr.find_element(By.XPATH,'./div[@class="col-5"]').text
                col7 = tr.find_element(By.XPATH,'./div[@class="col-6"]').text
                col8 = tr.find_element(By.XPATH,'./div[@class="col-7"]').text
                row_data = {
                    "ФИО": col1,
                    "Ссылка": col2,
                    "Специальность": col3,
                    "Место работы": col4,
                    "Город/регион": col5,
                    "Свои защиты": col6,
                    "Чужие защиты": col7,
                    "Публикации": col8
                }
                data_list.append(row_data)
            sql = """
                    INSERT INTO `dissernet_step1` (`fio`, `href`, `specialty`, `work`, `city`, `my`, `other`, `publication`, `data_update`) 
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s) 
                    ON DUPLICATE KEY UPDATE 
                    `fio` = VALUES(`fio`), 
                    `specialty` = VALUES(`specialty`), 
                    `work` = VALUES(`work`),
                    `city` = VALUES(`city`),
                    `my` = VALUES(`my`),
                    `other` = VALUES(`other`),
                    `publication` = VALUES(`publication`),
                    `data_update` = VALUES(`data_update`)
                """

            val = [(item["ФИО"], item["Ссылка"], item["Специальность"],item["Место работы"],item["Город/регион"],item["Свои защиты"],item["Чужие защиты"],item["Публикации"], datetime.datetime.now()) for item in data_list]

            cursor.executemany(sql, val)
            conn.commit()
    except mysql.connector.Error as err:
        print(f"Ошибка при добавлении данных: {err}")
    finally:
        # Закрытие курсора и соединения
        cursor.close()
        conn.close()