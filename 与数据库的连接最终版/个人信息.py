import pymysql
#与数据库进行连接
db=pymysql.connect("localhost","root","","test")
cursor=db.cursor()#创建游标
file =open('C:/Users/haome/Desktop/demo_信息.txt',encoding='utf-8',errors='ignore')
i=0
#按行读取文本内容
context=file.readline()
while 1:
    context=file.readline()
    context=context.replace(' ','')#将字符之间的空格删除
    i=i+1
    if i==6:
        weibo_id=context.replace('\n','')
    elif i==12:
        di_qu=context.replace('\n','')
    elif i==15:
        miao_shu=context.replace('\n','')
    elif i==16:
        guanzhu=context.replace('\n','')
        #将数字2万修改为20000，并设置为int类型
        guanzhu=int(guanzhu.replace('万','0000').replace('关注',''))
    elif i==17:
        fans=context.replace('\n','')
        fans=int(fans.replace('万','0000').replace('粉丝',''))
    elif i==18:
        weibo_num=context.replace('\n','')
        weibo_num=int(weibo_num.replace('万','0000').replace('微博',''))
    elif i==21:
        tag=context.replace('\n','').replace('标签：','')
    elif i==22:
        education=context.replace('\n','').replace('教育信息：','')
    elif i==23:
        professional=context.replace('\n','').replace('职业信息：','')
    elif i==25:
        i=0
        db.begin()
        try:
            #mysql语句输入数据
            sql="insert into weibo(id,di_qu,miao_shu,guanzhu,fans,weibo_num,tag,education,professional) values ('%s','%s','%s','%d','%d','%d','%s','%s','%s')"%(weibo_id,di_qu,miao_shu,guanzhu,fans,weibo_num,tag,education,professional)
            db.ping(reconnect=True)#如果发生断开则自动与数据库进行连接
            cursor.execute(sql)#执行mysql语句
            db.commit()#提交信息
        except Exception as e:
            db.close()#如果发生错误，则关闭游标
    if len(context) ==0:
        break
db.close()
file.close()
print('succeed')
