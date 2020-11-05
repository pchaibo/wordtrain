#encoding=utf-8
#导入pywin32包 pywin32
import win32com.client as win32
from win32com.client import gencache
from win32com.client import constants, gencache

def add_docx(files,savefile):
	#打开word软件
	word = win32.gencache.EnsureDispatch('Word.Application')
	#非可视化运行
	word.Visible = False

	output = word.Documents.Add()#新建合并后空白文档

	#需要合并的文档路径，这里有个文档1.docx，2.docx，3.docx.
	#files = ['D://python//demo//100.docx', 'D://python//demo//200.docx', 'D://python//demo//300.docx'] 
	#files = ['D:/python/demo/300.docx', 'D://python//demo/200.docx', 'D://python//demo//100.docx'] 
	for file in files:
		output.Application.Selection.Range.InsertFile(file)#拼接文档

	#获取合并后文档的内容
	doc = output.Range(output.Content.Start, output.Content.End)
	doc.Font.Name = "黑体"	#设置字体
	#savefile='D://python//demo//600.docx'
	output.SaveAs(savefile) #保存
	output.Close()

#add_docx(12)


def createPdf(wordPath, pdfPath):
	word = gencache.EnsureDispatch('Word.Application')
	doc = word.Documents.Open(wordPath, ReadOnly=1)
	doc.ExportAsFixedFormat(pdfPath,
	                        constants.wdExportFormatPDF,
	                        Item=constants.wdExportDocumentWithMarkup,
	                        CreateBookmarks=constants.wdExportCreateHeadingBookmarks)
	word.Quit(constants.wdDoNotSaveChanges)





