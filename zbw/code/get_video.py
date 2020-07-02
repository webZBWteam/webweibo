from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
import urllib
import urllib.request
import requests
import glob
import time
import pymysql

def error(brower):
    print('error!!!!!')
    brower.quit()

brower=webdriver.Safari()
url=input('请输入你要下载视频的网站（目前只支持好看视频）：')
name=input('输入你希望保存的文件名：')
try:
    brower.get(url)
except:
    error(brower)
time.sleep(2)
try:
    download=brower.find_element_by_xpath('//video[@mediatype="video"]')
    download=download.get_attribute('src')
except:
    error(brower)
r=requests.get(download)
with open(name+'.mp4','wb') as fp:
    fp.write(r.content)




brower.quit()