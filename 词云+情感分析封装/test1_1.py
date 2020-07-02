from DATA import data

file_name=input('请输入文件名:')
file_name='乘风破浪的姐姐'+file_name
a=data(file_name)
if '1' in file_name:
    a.read_all()
elif '2' in file_name:
    a.read_weibo()
elif '3' in file_name:
    a.read_pinglun()
a.mood()
a.ciyun()
