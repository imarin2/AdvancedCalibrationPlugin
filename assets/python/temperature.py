def preHeatExtruder():
	macro("M104 S180","ok",3,"Pre Heating Nozzle (fast) ",20)
def coolDownExtruder():
	macro("M104 S0","ok",50,"Shutting down Extruder",1)
def heatUpExtruder():
	macro("M104 S190","ok",5,"Heating Nozzle. Get ready to push...",5) #heating and waiting.
def heating2():
	macro("M104 S200","ok",90,"Heating extruder",1)
