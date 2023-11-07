from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait



from time import sleep
options = webdriver.ChromeOptions()


# options.binary_location = '/usr/bin/google-chrome-stable"'
options.add_argument('--headless')
options.add_argument('--no-sandbox')
options.add_argument('--disable-dev-shm-usage')
options.add_argument("--window-size=1024,768")

chrome_driver_path = "/usr/local/share/chromedriver-linux64"
service = Service(chrome_driver_path)

driver = webdriver.Chrome(service=service,options=options)
driver.get("http://localhost:8000")

print(driver.title)
