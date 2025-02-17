import wordcloud as wc
import jieba
import matplotlib.pyplot as plt
import imageio
from snownlp import SnowNLP
import numpy as np

plt.rc('figure', figsize=(15, 15))


class cloud:
    def __init__(self, name):
        self.dl_name = name
        self.name = 'C:/Users/haome/Desktop/data/' + name + '.txt'
        self.all_text = ''

    # 读取微博正文内容
    def read_weibo(self):
        file = open(self.name, encoding='utf-8')
        i = 0
        while 1:
            context = file.readline()
            self.all_text = self.all_text + context
            if len(context) == 0:
                break
        file.close()
        print('succeed0')

    # 读取微博评论内容，避开ID
    def read_pinglun(self):
        file = open(self.name, encoding='utf-8', errors='ignore')
        i = 0
        while 1:
            context = file.readline()
            i = i + 1
            if (i == 4):
                self.all_text = self.all_text + context
                i = 0
            if len(context) == 0:
                break
        file.close()
        print('succeed0')

    # 读取微博+评论文档，避开无关内容
    def read_all(self):
        file = open(self.name, encoding='utf-8', errors='ignore')
        i = 0
        context = file.readline()
        while 1:
            if '-----微博正文-----' in context:
                i = 1
                context = file.readline()
                self.all_text = self.all_text + context
                while 1:
                    context = file.readline()
                    if '-----微博正文-----' in context:
                        break
                    if len(context) == 0:
                        break
                    i = i + 1
                    if (i == 4):
                        self.all_text = self.all_text + context
                        i = 0
            if len(context) == 0:
                break
        file.close()
        print('succeed0')

    # 对每条内容进行情感分析
    def mood(self):
        self.all_text = self.all_text.replace(' ', '')
        sentimentslist = []
        for i in self.all_text:
            s = SnowNLP(i)
            sentimentslist.append(s.sentiments)
        plt.hist(sentimentslist, bins=np.arange(0, 1, 0.01), facecolor='b')
        plt.xlabel('情绪指数')
        plt.ylabel('分词数量')
        plt.title('情感分析图')
        plt.rcParams['font.sans-serif'] = ['SimHei']
        di_zhi = 'C:/Users/haome/Desktop/jieguo/情感' + self.dl_name + '.jpg'
        plt.savefig(di_zhi)

    #        plt.show()

    # 词云的生成与数据的处理
    def ciyun(self):
        # 去除没有意义的虚词，虚词在unless文件中保存
        excludes = open(file='C:/Users/haome/Desktop/unless.txt', encoding='utf-8').read().splitlines()
        for i in excludes:
            self.all_text = self.all_text.replace(i, '')
        self.all_text = self.all_text.replace(' ', '')
        print('succeed1')

        # 利用jieba库进行分词处理，某些人名需要作为新词加入词库
        jieba.load_userdict('C:/Users/haome/Desktop/name.txt')
        all_seg = jieba.cut(self.all_text, cut_all=False)
        all_word = ' '
        for seg in all_seg:
            all_word = all_word + seg + ' '
        print('succeed2')

        # 利用worldcloud制造词云
        font = 'C:/Windows/Fonts/simfang.ttf'
        color_mask = imageio.imread("C:/Users/haome/Desktop/love.jpg")
        cloud = wc.WordCloud(font_path=font,  # 设置字体
                             background_color="white",  # 背景颜色
                             max_words=200,  # 词云显示的最大词数
                             mask=color_mask,  # 设置背景图片
                             max_font_size=150,  # 字体最大值
                             mode="RGBA",
                             width=500,
                             height=400,
                             collocations=False)
        # 绘制词云图
        mywc = cloud.generate(all_word)
        plt.imshow(mywc)
        plt.axis("off")
        di_zhi = 'C:/Users/haome/Desktop/jieguo/词云' + self.dl_name + '.jpg'
        plt.savefig(di_zhi)
        #        plt.show()
        print("succeed")
