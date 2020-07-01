from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
import glob
import time
import pymysql
import random

def error(brower):
    print('error!!!!!')
    brower.quit()

def word_count(nums):
    dict={}
    for it in nums:
        if it not in dict:
            dict[it] = 1
        else: dict[it] += 1
    return dict


brower=webdriver.Safari()
brower.get('https://search.bilibili.com')
brower.maximize_window()
t=[]
try:
    inp=brower.find_element_by_id('search-keyword')
    inp.send_keys('乘风破浪的姐姐')
    time.sleep(1)
except:
    error(brower)
try:
    clic=brower.find_element_by_xpath('//a[@class="searchBtn"]')
    clic.send_keys(Keys.ENTER)
    time.sleep(1)
except:
    error(brower)
while True:
    try:
        time.sleep(random.random())
        times = brower.find_elements_by_xpath('//span[@title="上传时间"]')
        for ti in times:
            t.append(str(ti.text).strip())
    except:
        error(brower)
    try:
        time.sleep(random.random())
        clic = brower.find_element_by_xpath('//button[@class="nav-btn iconfont icon-arrowdown3"]')
        clic.send_keys(Keys.ENTER)
    except:
        break
dic=word_count(t)
key=[]
val=[]
for k,v in dic.items():
    key.append(k)
    val.append(v)
print(key)
print(val)
brower.quit()



