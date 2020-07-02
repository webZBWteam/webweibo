import requests
import sys
sys.path.append('/Users/zhubowen/Desktop/Web-Crawler/zbw/code')
from crawler5 import crawler
file=open('test.txt','w')
conten=crawler('https://ucan.alibabadesign.com/?spm=a313x.7781069.1998910419.27')
file.writelines(conten.brower.page_source)
file.flush()