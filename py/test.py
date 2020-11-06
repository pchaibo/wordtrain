#!/usr/bin/python
# -*- coding: UTF-8 -*-
import json
import urllib3
import requests
import toword
import time
domainname = "http://demo.com/" #网站域名
websitedirectory = "D:/wamp/web/ks/public" #网站public目录
def testdata():
	#import os
	#  忽略警告：InsecureRequestWarning: Unverified HTTPS request is being made. Adding certificate verification is strongly advised.
	requests.packages.urllib3.disable_warnings()
	# 一个PoolManager实例来生成请求, 由该实例对象处理与线程池的连接以及线程安全的所有细节
	http = urllib3.PoolManager()
	# 通过request()方法创建一个请求：
	r = http.request('GET', domainname + 'user/apiword/userquestion')
	#print(r.status) # 200
	# 获得html源码,utf-8解码
	data = r.data.decode()

	#print(data)
	json_str = json.loads(data)
	#print(json_str)
	if int(json_str['code'])==200:
		#exit(1)
		print('not data')
		return 

	#处理问题
	#src = "D:/wamp/web/ks/public"
	src = websitedirectory
	userword = src + "/uploads/userfile/" + json_str['word_url'] + ".docx"
	userpdf = src + "/uploads/userfile/" + json_str['word_url'] + ".pdf"
	arr =[]
	for ask in json_str['ask']:
		url =src + ask
		#print(url)
		arr.append(url)

	#print(usersrc)
	toword.add_docx(arr,userword)

	## 生成答案
	useranswer = src + "/uploads/userfile/" + json_str['word_url'] + "_answer.docx"
	arr =[]
	for ask in json_str['answer']:
		url =src + ask
		#print(url)
		arr.append(url)

	toword.add_docx(arr,useranswer)

	####处理数据
	r = http.request('GET', domainname + 'user/apiword/setquestion?id=' + str(json_str['questionid']) )
	data = r.data.decode()
	print(data)
	time.sleep(1)

	###生成pdf
	toword.createPdf(userword,userpdf)

#循环数据
counter = 1
while counter <= 5:
    testdata()
    time.sleep(5)


