sudo /usr/bin/avrdude -D -q -V -p atmega1280 -C /etc/avrdude.conf -c arduino -b 57600 -P  /dev/ttyAMA0   -U flash:w:/var/www/fabui/application/plugins/advancedBedCalibration/assets/Marlin.cpp.hex:i
# /usr/bin/avrdude-original -D -q -V -p atmega1280 -C /etc/avrdude.conf -c arduino -b 57600 -P /dev/ttyAMA0 -U flash:w:./Marlin.cpp.hex:i
