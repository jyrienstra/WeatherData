import os

filter_list = []
filter_file = open('data/filter.csv', 'r')
for line in filter_file:
    filter_list.append(line.strip('\n'))
filter_file.close()

for station in filter_list:
    try:
        os.remove("data/{}.csv".format(station))
    except:
        pass

for station in filter_list:
    if not os.path.isfile("data/{}.csv".format(station)):
        station_file = open("data/{}.csv".format(station), 'w+')
        station_file.write("Date,Time,Temperature,Dewpoint,Visibility,Humidity\n")
        station_file.close()
