import pymysql
di_qu_list=['北京','上海','重庆','天津','内蒙古','新疆','广西','宁夏','西藏','河北','山西','辽宁','吉林','黑龙江','江苏','浙江','安徽','福建','江西','山东','台湾','河南','湖北','湖南','广东','海南','四川','贵州','云南','陕西','甘肃','青海','香港','澳门','其他','海外']
di_qu_dict={'北京':0,'上海':0,'重庆':0,'天津':0,'内蒙古':0,'新疆':0,'广西':0,'宁夏':0,'西藏':0,'河北':0,'山西':0,'辽宁':0,'吉林':0,'黑龙江':0,'江苏':0,'浙江':0,'安徽':0,'福建':0,'江西':0,'山东':0,'台湾':0,'河南':0,'湖北':0,'湖南':0,'广东':0,'海南':0,'四川':0,'贵州':0,'云南':0,'陕西':0,'甘肃':0,'青海':0,'香港':0,'澳门':0,'其他':0,'海外':0}
i=0
db=pymysql.connect("localhost","root","","test")
cursor=db.cursor()
sql="select di_qu from weibo"
cursor.execute(sql)
for i in range(1,1306):
    result=cursor.fetchone()
    for province in di_qu_list:
        if province in str(result):
            di_qu_dict[province]=int(di_qu_dict[province])+1
            break
for i in di_qu_list:
    print(i)
    print(di_qu_dict[i])
db.close()
