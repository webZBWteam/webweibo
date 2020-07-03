import sys
sys.path.append('/Users/zhubowen/Desktop/Web-Crawler/zbw/code')
from crawler5 import crawler
brower=crawler('http://weibo.com')
brower.log_in()
brower.database_link()
#brower.db_load_user_id()
brower.db_load_user_info()