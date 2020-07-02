#!/usr/bin/env python
# -*- coding:utf-8 -*-
import tkinter
import crawler5
import cloud
from tkinter.filedialog import askdirectory
win = tkinter.Tk()
win.title("weibo_word_cloud")
win.geometry("400x400+200+50")

key_cord=''



def selectPath():
    path_ = askdirectory()
    path.set(path_)



def showinfo():
    # 获取输入的内容
    key_cord=entry.get()
    print(key_cord)

    root = tkinter.Tk()
    path = tkinter.StringVar()
    tkinter.Label(root, text="目标路径:").grid(row=0, column=0)
    tkinter.Entry(root, textvariable=path).grid(row=0, column=1)
    tkinter.Button(root, text="路径选择", command=selectPath).grid(row=0, column=2)

    root.mainloop()




entry = tkinter.Entry(win)
entry.pack()

button = tkinter.Button(win, text="确定", command=showinfo)
button.pack()

win.mainloop()





'''brower=crawler5.crawler('http://weibo.com')
brower.log_in()
#nums=eval(input('输入爬取的页数：'))
sears=key_cord+'/'
sears=sears.split('/')
#sears=input('输入爬虫关键词：').split('/')
brower.get_outcomes(1,sears)
#brower.get_outcomes(nums,sears)
cloud=cloud.data()
cloud.read_pinglun()
cloud.mood()
cloud.ciyun()
brower.brower.quit()'''