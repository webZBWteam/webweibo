import pymysql
student_num=0
worker_num=0
unknown_num=0
db=pymysql.connect("localhost","root","","test")
cursor=db.cursor()
sql='select education,professional from weibo'
cursor.execute(sql)
result=cursor.fetchall()
db.close()
for i in range(0,1305):
    education=result[i][0]
    professional=result[i][1]
    if professional!='':
        worker_num=worker_num+1
##        print(professional)
    else:
        if education!='':
            student_num=student_num+1
        else:
            unknown_num=unknown_num+1
print('student_num:%d' %(student_num))
print('worker_num:%d' %(worker_num))
print('unknown_num:%d' %(unknown_num))
