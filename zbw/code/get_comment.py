import glob
def load_txt():  # 导入txt文件名
    try:
        txts = glob.glob('/Users/zhubowen/Desktop/Web-Crawler/zbw/dataset/*3.txt')
        return txts
    except:
        print('no such txt found!!!!!')


def read_txt(txt,file):  # 读取txt文件中的内容
    txt1 = open(txt)
    txt2 = open(txt)
    txt1.readline()
    names = []
    while True:
        count=0
        content1 = txt1.readline()
        content2 = txt2.readline()
        if content1 == '                        ：\n':
            comments.writelines(content2.lstrip()[0:-1]+':')
            #print(content2.lstrip()[0:-1]+':',end='')
            content1=txt1.readline()
            content2 = txt2.readline()
            comments.writelines(content1.strip()+'\n')
            #print(content1.strip())
            count=count+1
            if count>50:
                break
        elif len(content1) == 0:
            return names

comments=open('comments.txt','w')
txts=load_txt()
for txt in txts:
    comments.writelines(txt+'\n')
    read_txt(txt,comments)