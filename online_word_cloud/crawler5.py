from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
import glob
import time
import pymysql

class crawler:
    def __init__(self,url):#初始化crawler类，实现浏览器的初始化和网页的打开
        try:
            self.brower = webdriver.Safari()
            self.url = url
            self.brower.get(self.url)
            self.wait = self.waite(self.brower)
            self.brower.maximize_window()
            time.sleep(10)
        except:
            self.web_error()
    def waite(self,brower):#设置显式等待，防止由于页面未成功夹在所出现对错误
        return WebDriverWait(brower,5)
    def web_error(self):#错误处理，输出错误信息，并将浏览器关闭
        self.brower.quit()
        print('error!!!!!')
    def database_error(self):#数据库错误处理
        self.conn.close()
        print('database error!!')
    def quit(self):#完成爬虫任务，退出页面
        print('succeed!!!!!')
        self.brower.quit()
    def log_in(self):#登录微博账号，这里需要用户扫描二维码，并且根据提示确认扫描成功
        try:
            time.sleep(1)
            clic = self.brower.find_element_by_xpath('//a[@href="javascript:void(0)"]')
            time.sleep(1)
            clic.click()
            time.sleep(1)
            clic = self.brower.find_element_by_xpath('//a[@action-data="tabname=qrcode"]')
            clic.click()
            time.sleep(5)
            confirm=input('enter "yes" to confirm you have scan the code')
            if confirm=='yes':
                self.brower.refresh()
            time.sleep(2)
            clic = self.brower.find_element_by_xpath('//a[@node-type="searchSubmit"]')
            clic.send_keys(Keys.ENTER)
        except:
            self.web_error()
    def search(self,sear):#搜索关键词
        time.sleep(2)
        try:
            inp = self.brower.find_element_by_xpath('//input[@node-type="text"]')
            time.sleep(2)
            inp.send_keys(sear)
            time.sleep(2)
            clic = self.brower.find_element_by_xpath('//button[@node-type="submit"]')
            time.sleep(2)
            clic.send_keys(Keys.ENTER)
        except:
            self.web_error()
    def open_comments(self):#打开所有评论
        try:
            time.sleep(3)
            clics = self.brower.find_elements_by_xpath('//a[@action-type="feed_list_comment"]')
            for clic in clics:
                clic.send_keys(Keys.ENTER)
                time.sleep(1)
        except:
            print('no comment!!!')
    def open_weibo(self):#打开所有微博
        try:
            clics = self.brower.find_elements_by_xpath('//a[@action-type="fl_unfold"]')
            for clic in clics:
                clic.send_keys(Keys.ENTER)
                time.sleep(1)
        except:
            print('no weibo to open!!!')
    def find_weibo(self):#找到所有微博的位置
        time.sleep(1)
        try:
            txts = self.brower.find_elements_by_xpath('//p[@node-type="feed_list_content"]')
            return txts
        except:
            self.web_error()
    def find_comments_position(self):#找到所有评论的位置
        time.sleep(1)
        try:
            comments = self.brower.find_elements_by_xpath('//div[@class="card-together"]')
            return comments
        except:
            self.web_error()
    def find_comments_detail(self,comment):#找到所有评论的内容
        time.sleep(1)
        try:
            infos = comment.find_elements_by_xpath('//div[@class="txt"]')
            return infos
        except:
            print('no comments!!!!!')
    def next_page(self):#实现翻页功能
        try:
            clic = self.brower.find_element_by_xpath('//a[@class="next"]')
            clic.send_keys(Keys.ENTER)
        except:
            self.web_error()
    def names(self,sear):#定义文件名
        name=[]
        for i in range(1,4):
            name.append(sear+str(i)+'.txt')
        return name
    def get_outcomes(self,num,sears):#获取微博数据和评论数据，保存为txt格式，其中num参数表示获取数据的页数
        for sear in sears:
            self.search(sear)
            name=self.names(sear)
            file1 = open(name[0], 'w')  # 微博和评论
            file2 = open(name[1], 'w')  # 仅微博
            file3 = open(name[2], 'w')  # 仅评论
            for i in range(num):
                self.open_weibo()
                self.open_comments()
                txts = self.find_weibo()
                comments = self.find_comments_position()
                for txt, comment in zip(txts, comments):
                    file1.writelines('-----微博正文-----')
                    file1.writelines(txt.text)
                    file2.writelines(txt.text)
                    # print(txt.text)
                    infos = self.find_comments_detail(comment)
                    for info in infos:
                        file1.writelines(info.text)
                        file3.writelines(info.text)
                        # print(info.text)
                self.next_page()
    def database_link(self):#连接数据库
        try:
            self.conn = pymysql.Connect(host='127.0.0.1', port=3306, user='root', passwd='asdf1234', db='crawler',charset='utf8')
            self.conn.begin()
            self.cursor = self.conn.cursor()
        except:
            self.database_error()
    def load_txt(self):#导入txt文件名
        try:
            txts = glob.glob('/Users/zhubowen/Desktop/Web-Crawler/zbw/dataset/*3.txt')
            return txts
        except:
            print('no such txt found!!!!!')
    def read_txt(self,txt):#读取txt文件中的内容
        txt1 = open(txt)
        txt2 = open(txt)
        txt1.readline()
        names=[]
        while True:
            content1=txt1.readline()
            content2=txt2.readline()
            if content1 == '                        ：\n':
                names.append(content2.lstrip()[0:-2])
            elif len(content1) == 0:
                return names
    def db_insert_id(self,names):#将用户名称插入数据库中
        for name in names:
            try:
                self.cursor.execute('''INSERT INTO user_info VALUES("''' + name + '''")''')
                # print('''INSERT INTO user_info VALUES("'''+name+'''")''')
                self.conn.commit()
            except:
                print('already exist!!!!!')
    def db_load_user_id(self):#将用户id数据导入数据库中
        txts=self.load_txt()
        for txt in txts:
            names=self.read_txt(txt)
            self.db_insert_id(names)
    def find_user(self):#搜索用户信息
        try:
            time.sleep(1)
            clic = self.brower.find_element_by_xpath('//a[@title="找人"]')
            clic.send_keys(Keys.ENTER)
            time.sleep(1)
            clic = self.brower.find_element_by_xpath('//a[@title="昵称"]')
            clic.send_keys(Keys.ENTER)
        except:
            self.web_error()
        try:
            time.sleep(1)
            position=self.brower.find_element_by_xpath('//div[@class="card card-user-b s-pg16 s-brt1"]')
            return position
        except:
            return False
    def db_get_user_id(self):  # 从数据库中获取用户信息
        self.cursor.execute('SELECT * FROM user_info')
        res = self.cursor.fetchall()
        names=[]
        for name in res:
            names.append(list(name)[0])
            #print(list(i)[0])
        return names
    def db_load_user_info(self):#向数据库中导入用户信息
        names=self.db_get_user_id()
        file=open('user_info.txt','w')
        for name in names:
            self.search(name)
            position=self.find_user()
            if position:
                file.writelines('-----user_info------')
                file.flush()
                file.writelines(position.text)
                file.flush()
                #print('-----user_info------')
                #print(position.text)
                file.writelines(self.get_gender(position))
                file.flush()
                #print(self.get_gender(position))
    def get_gender(self,position):#获取用户性别信息
        try:
            position.find_element_by_xpath('//i[@class="icon-sex icon-sex-female"]')
            return 'FEMALE'
        except:
            try:
                position.find_element_by_xpath('//i[@class="icon-sex icon-sex-male"]')
                return 'MALE'
            except:
                return 'SECRET'