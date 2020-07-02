from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
import glob
import time
def error(brower):
    brower.quit()

searss=['阿朵', '白冰', '陈松伶', '丁当', '黄圣依', '黄龄', '海陆', '金晨', '金莎', '蓝盈莹', '李斯丹妮', '刘芸', '孟佳', '吴昕', '沈梦辰', '王丽坤', '王霏霏', '王智', '许飞', '郁可唯', '伊能静', '袁咏琳', '张雨绮', '张含韵', '郑希怡', '朱婧汐']
sears=['沈梦辰', '王丽坤', '王霏霏', '王智', '许飞', '郁可唯', '伊能静', '袁咏琳', '张雨绮', '张含韵', '郑希怡', '朱婧汐']


brower=webdriver.Safari()
brower.get('https://baike.baidu.com')
for sear in sears:
    try:
        inpu=brower.find_element_by_xpath('//input[@id="query"]')
        inpu.send_keys(sear)
        time.sleep(1)
        clic=brower.find_element_by_xpath('//button[@id="search"]')
        clic.send_keys(Keys.ENTER)
        time.sleep(2)
        prin=brower.find_element_by_xpath('//h2[@class="lemma-summary"]')
        time.sleep(1)
        print(sear)
        print(prin.text)
        brower.quit()
        brower=webdriver.Safari()
        brower.get('https://baike.baidu.com')
        time.sleep(2)
    except:
        error(brower)
brower.quit()
