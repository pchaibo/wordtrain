#!/usr/bin/python
# -*- coding: UTF-8 -*-
import json
import urllib3
import requests
import toword
import time

def testdata():
	#import os
	#  忽略警告：InsecureRequestWarning: Unverified HTTPS request is being made. Adding certificate verification is strongly advised.
	requests.packages.urllib3.disable_warnings()
	# 一个PoolManager实例来生成请求, 由该实例对象处理与线程池的连接以及线程安全的所有细节
	http = urllib3.PoolManager()
	# 通过request()方法创建一个请求：
	r = http.request('GET', 'http://demo.com/user/apiword/userquestion')
	#print(r.status) # 200
	# 获得html源码,utf-8解码
	#print(r.data.decode())
	data = r.data.decode()

	#print(data)
	json_str = json.loads(data)
	#print(json_str)
	if int(json_str['code'])==200:
		#exit(1)
		print('not data')
		return 

	#处理问题
	src = "D:/wamp/web/ks/public"
	userword ="D:/wamp/web/ks/public/uploads/userfile/" + json_str['word_url'] + ".docx"
	userpdf ="D:/wamp/web/ks/public/uploads/userfile/" + json_str['word_url'] + ".pdf"
	arr =[]
	for ask in json_str['ask']:
		url =src + ask
		#print(url)
		arr.append(url)

	#print(arr)
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
	r = http.request('GET', 'http://demo.com/user/apiword/setquestion?id=' + str(json_str['questionid']) )
	data = r.data.decode()
	print(data)
	time.sleep(1)

	###生成pdf
	toword.createPdf(userword,userpdf)

#循环数据
counter = 1
while counter <= 5:
    #print("5555",counter)
    testdata()
    time.sleep(5)
   # counter = counter + 1


