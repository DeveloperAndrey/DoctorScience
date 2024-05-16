import sys
import mysql.connector    
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

# browser = webdriver.Edge(service=EdgeService(EdgeChromiumDriverManager().install()))
options = Options()
options.add_argument('--headless')
options.add_argument('--no-sandbox')
options.add_argument('--disable-dev-shm-usage')
browser = webdriver.Chrome(service=ChromiumService(ChromeDriverManager().install()), options=options)
browser.implicitly_wait(10)

new_data = '2024-01-30'
old_data = '2000-01-01'

# Объявления о защитах ВАК
count_1 = f'https://vak.minobrnauki.gov.ru/az/server/php/counter.php?cmd=%20SELECT%20%20a1.date_zach%20AS%20a__date_zach%2C%20a1.sois_fam%20AS%20a__sois_fam%2C%20a1.sois_imy%20AS%20a__sois_imy%2C%20a1.sois_otch%20AS%20a__sois_otch%2C%20a1.id%20AS%20a__id%2C%20a1.name_dis%20AS%20a__name_dis%20FROM%20%20att_advert%20a1%20LEFT%20JOIN%20%28%20att_case%20a2%20LEFT%20JOIN%20dc3_council%20a3%20ON%20a2.council_id%20%3D%20a3.id%20%29%20ON%20a1.att_id%20%3D%20a2.id%20WHERE%20%20%28%20a1.date_publ%20IS%20NOT%20NULL%20AND%20a1.date_publ%20%21%3D%20%271970-01-01%27%20AND%20a1.date_zach%20IS%20NOT%20NULL%20AND%20a2.is_deleted%20IS%20FALSE%20AND%20a2.council_id%20%21%3D%201%20AND%20a3.is_vak%20AND%20a1.att_id%20%21%3D%20100057747%20AND%20a1.version%20IN%20%28%20SELECT%20%20MAX%28%20a4.version%29%20FROM%20%20att_advert%20a4%20WHERE%20%20a4.date_publ%20IS%20NOT%20NULL%20AND%20a4.id%20%3D%20a1.id%29%20%29%20AND%20%28%281%20%3D%201%29%20AND%20%28%20a2.type_dis%20IN%20%28%20%3F%20%29%29%20AND%20%28%28%20a1.shifr_nauch_spec%20IN%20%28%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%20%29%29%29%20AND%20%28%20a1.date_zach%20%3E%3D%20%3F%29%20AND%20%28%20a1.date_zach%20%3C%3D%20%3F%29%29%20ORDER%20BY%20%20a1.date_zach%20ASC%2C%20a1.sois_fam%20ASC%2C%20a1.sois_imy%20ASC%20LIMIT%2020&args%5B0%5D=2&args%5B1%5D=01.04.07&args%5B2%5D=02.00.15&args%5B3%5D=05.02.08&args%5B4%5D=05.02.10&args%5B5%5D=05.04.02&args%5B6%5D=05.05.03&args%5B7%5D=05.09.03&args%5B8%5D=05.16.01&args%5B9%5D=05.23.01&args%5B10%5D=05.23.04&args%5B11%5D=07.00.02&args%5B12%5D=08.00.05&args%5B13%5D=10.02.01&args%5B14%5D=12.00.01&args%5B15%5D=12.00.02&args%5B16%5D=12.00.08&args%5B17%5D=12.00.09&args%5B18%5D=13.00.01&args%5B19%5D=13.00.02&args%5B20%5D=13.00.08&args%5B21%5D=1.3.8.&args%5B22%5D=1.4.14.&args%5B23%5D=2.1.1.&args%5B24%5D=2.1.4.&args%5B25%5D=2.4.2.&args%5B26%5D=2.4.7.&args%5B27%5D=2.5.11.&args%5B28%5D=2.5.6.&args%5B29%5D=2.5.8.&args%5B30%5D=2.6.1.&args%5B31%5D=5.1.1.&args%5B32%5D=5.1.2.&args%5B33%5D=5.1.4.&args%5B34%5D=5.2.3.&args%5B35%5D=5.6.1.&args%5B36%5D=5.8.1.&args%5B37%5D=5.8.2.&args%5B38%5D=5.8.7.&args%5B39%5D=5.9.5.&args%5B40%5D={old_data}&args%5B41%5D={new_data}'
data_1 = f'https://vak.minobrnauki.gov.ru/ais/vak/templates/vak_new/adverts_list.php.t?cmd=T:advert&args[]=*WHERE%20(1%20%3D%201)%20AND%20(a.att_id.type_dis%20IN%20(%20%3F%20))%20AND%20((a.shifr_nauch_spec%20IN%20(%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%20)))%20AND%20(a.date_zach%20%3E%3D%20%3F)%20AND%20(a.date_zach%20%3C%3D%20%3F)&args[]=2&args[]=01.04.07&args[]=02.00.15&args[]=05.02.08&args[]=05.02.10&args[]=05.04.02&args[]=05.05.03&args[]=05.09.03&args[]=05.16.01&args[]=05.23.01&args[]=05.23.04&args[]=07.00.02&args[]=08.00.05&args[]=10.02.01&args[]=12.00.01&args[]=12.00.02&args[]=12.00.08&args[]=12.00.09&args[]=13.00.01&args[]=13.00.02&args[]=13.00.08&args[]=1.3.8.&args[]=1.4.14.&args[]=2.1.1.&args[]=2.1.4.&args[]=2.4.2.&args[]=2.4.7.&args[]=2.5.11.&args[]=2.5.6.&args[]=2.5.8.&args[]=2.6.1.&args[]=5.1.1.&args[]=5.1.2.&args[]=5.1.4.&args[]=5.2.3.&args[]=5.6.1.&args[]=5.8.1.&args[]=5.8.2.&args[]=5.8.7.&args[]=5.9.5.&args[]={old_data}&args[]={new_data}&args[]=OFFSET%3A'
# Самостоятельное присуждение степеней
count_2 = f'https://vak.minobrnauki.gov.ru/az/server/php/counter.php?cmd=%20SELECT%20%20a1.defend_date%20AS%20a__defend_date%2C%20a1.last_name%20AS%20a__last_name%2C%20a1.first_name%20AS%20a__first_name%2C%20a1.middle_name%20AS%20a__middle_name%2C%20a1.case_id%20AS%20a__case_id%2C%20a1.dissertation_name%20AS%20a__dissertation_name%20FROM%20%20adverts%20a1%20LEFT%20JOIN%20%28%20cases%20a2%20LEFT%20JOIN%20%28%20council_states%20a3%20LEFT%20JOIN%20councils%20a4%20ON%20a3.council%20%3D%20a4.id%20%29%20ON%20a2.council_state%20%3D%20a3.id%20%29%20ON%20a1.case_id%20%3D%20a2.id%20WHERE%20%20%28%20a1.publication_date%20IS%20NOT%20NULL%20AND%20a1.defend_date%20IS%20NOT%20NULL%20AND%20a1.version%20IN%20%28%20SELECT%20%20MAX%28%20a5.version%29%20FROM%20%20adverts%20a5%20WHERE%20%20a5.publication_date%20IS%20NOT%20NULL%20AND%20a5.case_id%20%3D%20a1.case_id%29%20AND%20a4.org_union%20NOT%20IN%20%280%2C4%29%20%29%20AND%20%28%281%20%3D%201%29%20AND%20%28%28%20a1.speciality%20IN%20%28%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%20%29%29%29%20AND%20%28%20a1.defend_date%20%3E%3D%20%3F%29%20AND%20%28%20a1.defend_date%20%3C%3D%20%3F%29%29%20ORDER%20BY%20%20a1.defend_date%20ASC%2C%20a1.last_name%20ASC%2C%20a1.first_name%20ASC%20LIMIT%2020&args%5B0%5D=01.04.07&args%5B1%5D=02.00.15&args%5B2%5D=05.02.08&args%5B3%5D=05.02.10&args%5B4%5D=05.04.02&args%5B5%5D=05.05.03&args%5B6%5D=05.09.03&args%5B7%5D=05.16.01&args%5B8%5D=05.23.01&args%5B9%5D=05.23.04&args%5B10%5D=07.00.02&args%5B11%5D=08.00.05&args%5B12%5D=10.02.01&args%5B13%5D=12.00.01&args%5B14%5D=12.00.02&args%5B15%5D=12.00.08&args%5B16%5D=12.00.09&args%5B17%5D=13.00.01&args%5B18%5D=13.00.02&args%5B19%5D=13.00.08&args%5B20%5D=1.3.8.&args%5B21%5D=1.4.14.&args%5B22%5D=2.1.1.&args%5B23%5D=2.1.4.&args%5B24%5D=2.4.2.&args%5B25%5D=2.4.7.&args%5B26%5D=2.5.11.&args%5B27%5D=2.5.6.&args%5B28%5D=2.5.8.&args%5B29%5D=2.6.1.&args%5B30%5D=5.1.1.&args%5B31%5D=5.1.2.&args%5B32%5D=5.1.4.&args%5B33%5D=5.2.3.&args%5B34%5D=5.6.1.&args%5B35%5D=5.8.1.&args%5B36%5D=5.8.2.&args%5B37%5D=5.8.7.&args%5B38%5D=5.9.5.&args%5B39%5D={old_data}&args%5B40%5D={new_data}'
data_2 = f'https://vak.minobrnauki.gov.ru/ais/vak/templates/vak_new/adverts_list.php.t?cmd=T:independent&args[]=*WHERE%20(1%20%3D%201)%20AND%20((a.speciality%20IN%20(%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%2C%20%3F%20)))%20AND%20(a.defend_date%20%3E%3D%20%3F)%20AND%20(a.defend_date%20%3C%3D%20%3F)&args[]=01.04.07&args[]=02.00.15&args[]=05.02.08&args[]=05.02.10&args[]=05.04.02&args[]=05.05.03&args[]=05.09.03&args[]=05.16.01&args[]=05.23.01&args[]=05.23.04&args[]=07.00.02&args[]=08.00.05&args[]=10.02.01&args[]=12.00.01&args[]=12.00.02&args[]=12.00.08&args[]=12.00.09&args[]=13.00.01&args[]=13.00.02&args[]=13.00.08&args[]=1.3.8.&args[]=1.4.14.&args[]=2.1.1.&args[]=2.1.4.&args[]=2.4.2.&args[]=2.4.7.&args[]=2.5.11.&args[]=2.5.6.&args[]=2.5.8.&args[]=2.6.1.&args[]=5.1.1.&args[]=5.1.2.&args[]=5.1.4.&args[]=5.2.3.&args[]=5.6.1.&args[]=5.8.1.&args[]=5.8.2.&args[]=5.8.7.&args[]=5.9.5.&args[]={old_data}&args[]={new_data}&args[]=OFFSET%3A'

# Колличесво
browser.get(count_1)
count_declared = int(browser.find_element(By.XPATH,'.//body').text)
print(count_declared)

browser.get(count_2)
count_independent = int(browser.find_element(By.XPATH,'.//body').text)
print(count_independent)

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
        # Пример вставки одной строки данных
        # sql = "INSERT INTO statistics (must) VALUES (%s)"
        # val = [
        #     (count_declared,),
        #     (count_independent,)
        # ]
        # cursor.executemany(sql, val)
        sql = "UPDATE statistics SET must=%s, data_update=%s WHERE id = 1"
        val = (count_declared,datetime.datetime.now()) 
        cursor.execute(sql, val)
        sql = "UPDATE statistics SET must=%s, data_update=%s WHERE id = 2"
        val = (count_independent,datetime.datetime.now()) 
        cursor.execute(sql, val)
        # Подтверждение изменений в базе данных
        conn.commit()

        data_list = []
        i = 0
        while i < int(count_declared):
            browser.get(data_1 + str(i))
            table = browser.find_elements(By.XPATH,'//table[@class="table table-bordered table-hover table-striped"]//tr')
            for tr in table[1:]:
                col1 = tr.find_element(By.XPATH,'.//td[2]').text
                col2 = tr.find_element(By.XPATH,'.//td[3]').text
                col3 = tr.find_element(By.XPATH,'.//td[4]').text
                col4 = tr.find_element(By.XPATH,'.//td[4]/a').get_attribute("href")
                col5 = "Объявления о защитах ВАК"
                # Создаем словарь с данными из текущей строки
                row_data = {
                    "Дата защиты": col1,
                    "ФИО соискателя": col2,
                    "Наименование диссертации": col3,
                    "Наименование диссертации.Ссылка": col4,
                    "Вид защиты": col5
                }
                # Добавляем словарь в список
                data_list.append(row_data)
            i = i + 20
            sql = """
                INSERT INTO `vak_step1` (`date`, `fio`, `dissertation`, `href`, `type`, `data_update`) 
                VALUES (%s, %s, %s, %s, %s, %s) 
                ON DUPLICATE KEY UPDATE 
                `date` = VALUES(`date`), 
                `fio` = VALUES(`fio`), 
                `dissertation` = VALUES(`dissertation`),
                `type` = VALUES(`type`),
                `data_update` = VALUES(`data_update`)
            """

            val = [(datetime.datetime.strptime(item["Дата защиты"], "%d.%m.%Y").date(), item["ФИО соискателя"], item["Наименование диссертации"], item["Наименование диссертации.Ссылка"],item["Вид защиты"], datetime.datetime.now()) for item in data_list]

            cursor.executemany(sql, val)
            conn.commit()
            data_list = []
        data_list = []
        i = 0
        while i < int(count_independent):
            browser.get(data_2 + str(i))
            table = browser.find_elements(By.XPATH,'//table[@class="table table-bordered table-hover table-striped"]//tr')
            for tr in table[1:]:
                col1 = tr.find_element(By.XPATH,'.//td[2]').text
                col2 = tr.find_element(By.XPATH,'.//td[3]').text
                col3 = tr.find_element(By.XPATH,'.//td[4]').text
                col4 = tr.find_element(By.XPATH,'.//td[4]/a').get_attribute("href")
                col5 = "Самостоятельное присуждение степеней"
                # Создаем словарь с данными из текущей строки
                row_data = {
                    "Дата защиты": col1,
                    "ФИО соискателя": col2,
                    "Наименование диссертации": col3,
                    "Наименование диссертации.Ссылка": col4,
                    "Вид защиты": col5
                }
                # Добавляем словарь в список
                data_list.append(row_data)
            i = i + 20
            sql = """
                INSERT INTO `vak_step1` (`date`, `fio`, `dissertation`, `href`, `type`, `data_update`) 
                VALUES (%s, %s, %s, %s, %s, %s) 
                ON DUPLICATE KEY UPDATE 
                `date` = VALUES(`date`), 
                `fio` = VALUES(`fio`), 
                `dissertation` = VALUES(`dissertation`),
                `type` = VALUES(`type`),
                `data_update` = VALUES(`data_update`)
            """

            val = [(datetime.datetime.strptime(item["Дата защиты"], "%d.%m.%Y").date(), item["ФИО соискателя"], item["Наименование диссертации"], item["Наименование диссертации.Ссылка"],item["Вид защиты"], datetime.datetime.now()) for item in data_list]

            cursor.executemany(sql, val)
            conn.commit()
            data_list = []
        print("Данные успешно добавлены в таблицу")

    except mysql.connector.Error as err:
        print(f"Ошибка при добавлении данных: {err}")
    finally:
	    # Закрытие курсора и соединения
	    cursor.close()
	    conn.close()