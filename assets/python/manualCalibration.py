#bed leveling tool
import time
import sys, os
import serial
from subprocess import call
import numpy as np
import json
import pprint

s_warning=s_error=s_skipped=0

#track trace
def trace(string):
        print string
        return

def read_serial(gcode):
	serial.flushInput()
	serial.write(gcode + "\r\n")
	time.sleep(0.1)
	
	#return serial.readline().rstrip()
	response=serial.readline().rstrip()
	
	if response=="":
		return "NONE"
	else:
		return response
		
def macro(code,expected_reply,timeout,error_msg,delay_after,warning=False,verbose=True):
	global s_error
	global s_warning
	global s_skipped
	serial.flushInput()
	if s_error==0:
		serial_reply=""
		macro_start_time = time.time()
		serial.write(code+"\r\n")
		if verbose:
			trace(error_msg)
		time.sleep(0.3) #give it some tome to start
		while not (serial_reply==expected_reply or serial_reply[:4]==expected_reply):
			#Expected reply
			#no reply:
			if (time.time()>=macro_start_time+timeout+5):
				if serial_reply=="":
					serial_reply="<nothing>"
				if not warning:
					s_error+=1
					trace(error_msg + ": Failed (" +serial_reply +")")
				else:
					s_warning+=1
					trace(error_msg + ": Warning! ")
				return False #leave the function
			serial_reply=serial.readline().rstrip()
			#add safety timeout
			time.sleep(0.2) #no hammering
			pass
		time.sleep(delay_after) #wait the desired amount
	else:
		trace(error_msg + ": Skipped")
		s_skipped+=1
		return False
	return serial_reply

def measurePointHeight(x, y, initialHeight, feedrate):
	macro("M402","ok",2,"Retracting Probe (safety)",1, verbose=False)
	macro("G0 Z60 F5000","ok",5,"Moving to start Z height",10) #mandatory!
	
	
	macro("G0 X"+str(x)+" Y"+str(y)+" Z "+str(initialHeight)+" F10000","ok",2,"Moving to left down corner point",10)
	macro("M401","ok",2,"Lowering Probe",1, warning=True, verbose=False)
	serial.flushInput()
	serial_reply = ""
	probe_start_time=time.time()
	serial.write("G30\r\n")
	
	while not serial_reply[:22]=="echo:endstops hit:  Z:":
		serial_reply=serial.readline().rstrip()
	if (time.time() - probe_start_time>80): #timeout management
		z = initialHeight 
		serial.flushInput()
		return z
	pass
	if serial_reply!="":
		z=serial_reply.split("Z:")[1].strip()
	serial_reply=""
	serial.flushInput()
	macro("G0 X"+str(x)+" Y"+str(y)+" Z "+str(initialHeight)+" F10000","ok",2,"Moving to left down corner point",10)
	macro("M402","ok",2,"Raising Probe",1, warning=True, verbose=False)	
	return z

def getHighestValue(z1, z2, z3, z4):
	result = z1 
	if (z2>result):
		result = z2
	if (z3>result):
		result = z3
	if (z4>result):
		result = z4
	return result

trace("Manual Bed Calibration Wizard Initiated")


port = '/dev/ttyAMA0'
baud = 115200

#initialize serial
serial = serial.Serial(port, baud, timeout=0.6)
serial.flushInput()


macro("G90","ok",5,"Setting abs mode",0.1, verbose=False)
macro("M741","TRIGGERED",2,"Front panel door control",1, verbose=False)	
macro("M402","ok",2,"Retracting Probe (safety)",1, warning=True, verbose=False)	
#macro("G27","ok",100,"Homing Z - Fast",0.1)	
macro("G28","ok",100,"Homing all axis",0.1, verbose=False)

#macro("G92 Z241.2","ok",5,"Setting correct Z",0.1, verbose=False)


#M402 #DOUBLE SAFETY!
macro("M402","ok",2,"Retracting Probe (safety)",1, verbose=False)	
macro("G0 Z60 F5000","ok",5,"Moving to start Z height",10) #mandatory!


maxXPhys = 195 # originally 195 
minXPhys = 0 
maxYPhys = 175 
minYPhys = 0 

heightLeftDown = measurePointHeight(minXPhys, minYPhys, 45, 200)
heightLeftUp = measurePointHeight(minXPhys, maxYPhys, 45, 200)
heightRightUp = measurePointHeight(maxXPhys, maxYPhys, 45, 200)
heightRightDown = measurePointHeight(maxXPhys, minYPhys, 45, 200)

max = getHighestValue(heightLeftDown, heightLeftUp, heightRightUp, heightRightDown)

print heightLeftUp +"   "+heightRightUp
print heightLeftDown +"   "+heightRightDown
print "Maximum: "+max


sys.exit()
