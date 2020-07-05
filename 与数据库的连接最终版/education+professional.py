import pymysql
#对用户实现学生，工作者和未知身份的分类
student_num=0
worker_num=0
unknown_num=0
#与test数据库进行连接
db=pymysql.connect("localhost","root","","test")
#创建游标
cursor=db.cursor()
#mysql语句实现对教育信息，工作信息的查询
sql='select education,professional from weibo'
#执行代码
cursor.execute(sql)
#获取查询得到的全部信息，以元组形式进行展示
result=cursor.fetchall()
db.close()#关闭游标
for i in range(0,1305):
    #获取教育信息和职业信息
    education=result[i][0]
    professional=result[i][1]
    #如果职业信息不为空，则为工作者
    if professional!='':
        worker_num=worker_num+1
##        print(professional)
    #如果职业信息为空，教育信息不为空则为学生
    else:
        if education!='':
            student_num=student_num+1
        #如果二者皆为空，则说明用户没有表明自己的身份类型，是未知类
        else:
            unknown_num=unknown_num+1
print('student_num:%d' %(student_num))
print('worker_num:%d' %(worker_num))
print('unknown_num:%d' %(unknown_num))
