#bed leveling tool
import time
import sys, os
import serial
from subprocess import call
import numpy as np
import json
import pprint

maxXPhys = 195 # originally 195
minXPhys = 0
maxYPhys = 175
minYPhys = 0


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

def jogToBedCorner(corner, height, feedrate):
	serial.flushInput()
	macro("M402","ok",2,"Retracting Probe (safety)",1, verbose=False)
	corner = corner.upper()
	if (corner=="LU"):
		macro("G0 X"+str(minXPhys)+" Y"+str(minYPhys)+" Z "+str(height)+" F"+str(feedrate),"ok",2,"Moving to left down corner point",0.1)
	if (corner=="LO"):
		macro("G0 X"+str(minXPhys)+" Y"+str(maxYPhys)+" Z "+str(height)+" F"+str(feedrate),"ok",2,"Moving to left upper corner point",0.1)
	if (corner=="RU"):
		macro("G0 X"+str(maxXPhys)+" Y"+str(minYPhys)+" Z "+str(height)+" F"+str(feedrate),"ok",2,"Moving to right lower corner point",0.1)
	if (corner=="RO"):
		macro("G0 X"+str(maxXPhys)+" Y"+str(maxYPhys)+" Z "+str(height)+" F"+str(feedrate),"ok",2,"Moving to right upper corner point",0.1)


def getHighestValue(z1, z2, z3, z4):
	result = z1 
	if (z2>result):
		result = z2
	if (z3>result):
		result = z3
	if (z4>result):
		result = z4
	return result

global corner

try:
	corner = str(sys.argv[1])
	height = float(sys.argv[2])
	feedrate = int(sys.argv[3])
except:
	trace("Usage: python jogTo.py  corner height feedrate")
	trace("Corner is either")
	trace("LO for left upper corner")
	trace("LU for left lower corner")
	trace("RO for right upper corner")
	trace("RU for right lower corner")
	sys.exit()

trace("Manual Bed Calibration Wizard Initiated")


port = '/dev/ttyAMA0'
baud = 115200

#initialize serial
serial = serial.Serial(port, baud, timeout=0.6)
serial.flushInput()


macro("M741","TRIGGERED",2,"Front panel door control",1, verbose=False)	
macro("M402","ok",2,"Retracting Probe (safety)",1, warning=True, verbose=False)	
macro("G90","ok",5,"Setting abs mode",0.1, verbose=False)
#macro("G92 Z241.2","ok",5,"Setting correct Z",0.1, verbose=False)

macro("G0 Z60 F5000","ok",5,"Moving to start Z height",0.1) #mandatory!
jogToBedCorner(corner, height, feedrate)
	

sys.exit()
