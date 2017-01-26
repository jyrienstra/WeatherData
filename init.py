import os
import datetime
from sys import argv

def main(tomorrow=False):
    filter_list = []
    filter_file = open('data/filter.csv', 'r')

    print(tomorrow)

    if tomorrow:
        date_path = "data/{}".format(datetime.date.today() + datetime.timedelta(1))
    else:
        date_path = "data/{}".format(datetime.date.today())
    print(date_path)

    for line in filter_file:
        filter_list.append(line.strip('\n'))
    filter_file.close()

    for station in filter_list:
        try:
            os.remove("{}/{}.csv".format(date_path, station))
        except:
            pass

    for station in filter_list:

        if not os.path.exists(date_path):
            os.makedirs(date_path)
        if not os.path.isfile("{}/{}.csv".format(date_path, station)):
            station_file = open("{}/{}.csv".format(date_path, station), 'w+')
            station_file.write("Date,Time,Temperature,Dewpoint,Visibility,Humidity\n")
            station_file.close()


if __name__ == '__main__':
    print(len(argv))

    if len(argv) < 2:
        tomorrow = False
        print("1")
    elif argv[1] == "False":
        tomorrow = False
        print("2")
    elif argv[1] == "True":
        tomorrow = True
        print("3")
    else:
        tomorrow = False

    print(tomorrow)
    main(tomorrow)
