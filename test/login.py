from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.action_chains import ActionChains

# from selenium.webdriver.support import FluentWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from selenium.common.exceptions import NoSuchElementException

from time import sleep


options = webdriver.ChromeOptions()


# options.binary_location = '/usr/bin/google-chrome-stable"'
options.add_argument('--headless')
options.add_argument('--no-sandbox')
options.add_argument('--disable-dev-shm-usage')
options.add_argument("--window-size=1024,768")

chrome_driver_path = "/usr/bin/chromedriver"
service = Service(chrome_driver_path)

driver = webdriver.Chrome(service=service,options=options)


driver.get("http://localhost:8000/login")

driver.set_window_size(1919, 925)

email = driver.find_element(By.ID, "email")
email.click()
email.send_keys("nirmal@wl.com")

password = driver.find_element(By.ID, "password")
password.send_keys("Admin1234")
# password.send_keys("1234")

driver.find_element(By.ID,"submit").click()

# sleep(3)

wait = WebDriverWait(driver, timeout=10, poll_frequency=0.1, ignored_exceptions=[NoSuchElementException])
# id = alert
wait.until(EC.visibility_of_element_located((By.ID, "alert")))

text = driver.find_element(By.ID, "alert").text
assert "success" in text, "success not in text" 
# assert "Password must be at least 8 characters long" in text

print(text)

sleep(3)

driver.quit()

