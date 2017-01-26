import sys
import socket
from threading import Thread
import xml.etree.ElementTree as ET
from sys import exit
import subprocess
import datetime
import os

import init


def stationThread(connection, ip, port, MAX_BUFFER_SIZE=4096):
    check = True
    data = ""

    filter_list = []
    filter_file = open('data/filter.csv', 'r')

    for line in filter_file:
        filter_list.append(line.strip('\n'))

    while check:
        # Receiving data
        new_data = connection.recv(MAX_BUFFER_SIZE)

        # if data is bigger then the MAX_BUFFER_SIZE, notify the user.
        size = sys.getsizeof(new_data)
        data += new_data.decode()
        # Check the size of the data
        if size >= MAX_BUFFER_SIZE:
            print("Too many bytes, buffer too small: {}".format(size))
            data = new_data.decode()
        # When there is no data, stop the loop
        elif not new_data:
            break

        elif "<?xml version=\"1.0\"?>" in data:
            if "</WEATHERDATA>" in data:
                first, end_tag, buffer = data.partition('</WEATHERDATA>\n')

                # Parse the data to a readable format, XML
                tree = ET.fromstring(first + end_tag)

                # Retrieve data from xml string
                for parent in tree.findall('MEASUREMENT'):
                    data_list = [parent.find('STN').text, parent.find('DATE').text, parent.find('TIME').text,
                                 parent.find('TEMP').text, parent.find('DEWP').text, parent.find('VISIB').text]
                    # Check whether the station number is in the filter list
                    if data_list[0] in filter_list:

                        # Open the file of the station

                        temperature = data_list[3]
                        temperatureDewpoint = data_list[4]
                        humidity = -1
                        data_list.append(humidity)

                        # Check whether there is missing data in the measurement
                        if None not in data_list:
                            # Incase there is no missing data, write the new data to the file.
                            humidity = str(round((
                                100 * ((112 - (0.1 * float(temperature)) + float(temperatureDewpoint)) / (
                                    112 + (0.9 * float(temperature)))) ** 8), 2))
                            data_list[6] = humidity
                            writeToFile(data_list)

                        else:
                            # Incase there is missing data, extrapolate it.
                            print("{} - {},{},{},{},{},{}\n".format(data_list[0], data_list[1], data_list[2], data_list[3],
                                                                    data_list[4], data_list[5], data_list[6]))
                            data_list = extrapolate(data_list)
                            temperature = data_list[3]
                            temperatureDewpoint = data_list[4]

                            # Write the new data to the file.
                            data_list[6] = str(round((
                                100 * ((112 - (0.1 * float(temperature)) + float(temperatureDewpoint)) / (
                                    112 + (0.9 * float(temperature)))) ** 8), 2))
                            writeToFile(data_list)
                data = buffer
        else:
            data = new_data.decode()

    # Close the connection, when the loop stops
    connection.close()
    print("Connection {}:{} is terminated".format(ip, port))


def writeToFile(data_list):
    """
    :param file: What file to write to
    :param data_list: The data to write
    """
    date = datetime.date.today()
    date_path = "data/{}".format(date)

    csv_file = open("{}/{}.csv".format(date_path, data_list[0]), 'a')

    csv_file.write(
        "{},{},{},{},{},{}\n".format(data_list[1], data_list[2], data_list[3], data_list[4],
                                     data_list[5],
                                     data_list[6]))

    csv_file.close()


def weatherServer():
    """
    Open a weather server
    """
    port = 7789
    ip = "0.0.0.0"

    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

    # Try to bind the port, when it fails, stop the program
    try:
        server_socket.bind((ip, port))
        print("Listening for connections on port {}".format(port))
    except socket.error as msg:
        print("Failed to bind port {}.".format(port))
        sys.exit()

    # Start listening to the socket
    server_socket.listen(5)

    check = True

    # Infinite loop, for creating a thread per connection
    while check:
        connection, address = server_socket.accept()
        ip, port = str(address[0]), str(address[1])
        print("Connected on {}:{}".format(ip, port))

        # Try to start a thread for the connection
        try:
            Thread(target=stationThread, args=(connection, ip, port)).start()
        except:
            import traceback
            traceback.print_exc()
    server_socket.close()


def extrapolate(datalist):
    """
    :param datalist: A list with the measurement data, which contains missing data
    :return: A list with the measurement data, which contains the values calculated for the missing data
    """
    missing_list = []
    print(datalist[0])

    # Check which element of the list is missing data.
    for i in range(len(datalist)):
        if datalist[i] is None:
            missing_list.append(i - 1)

    # For all missing data in the list, retrieve the last 30 measurements (or less)
    for number in missing_list:
        previous_data = extrapolateRetrieveData(datalist, number)

        x = []

        # Create a x-axis
        for i in range(len(previous_data)):
            x.append(i)

        # Import numpy, importing numpy in all threads would cause alot of load, so importing it when needed reliefs
        # this.

        # Try to fit a curve on the data, with a 4 degree polynomial
        # If there are more then 6 measurements, calculate the missing value. Otherwise take the previous measurement.
        next_value = -1
        if len(previous_data) > 6:
            import numpy
            curve = numpy.polyfit(x, previous_data, 2)
            poly = numpy.poly1d(curve)

            # Calculate the next value in the sequence.
            next_value = round(poly(len(previous_data) + 1), 1)
            print(next_value)
        else:
            try:
                next_value = previous_data[-1]
            except IndexError:
                yesterday = datetime.date.today() - datetime.timedelta(1)
                date_path = "data/{}".format(yesterday)
                if os.path.isdir(date_path):
                    previous_data = extrapolateRetrieveData(datalist, number, True)
                    if not previous_data:
                        calc_curve(previous_data, x)
                    else:
                        print("Data was dropped")

        # Add the new value back to the datalist
        datalist[number + 1] = next_value
    print(missing_list)
    return datalist


def calc_curve(previous_data, x):
    import numpy
    curve = numpy.polyfit(x, previous_data, 2)
    poly = numpy.poly1d(curve)

    # Calculate the next value in the sequence.
    next_value = round(poly(len(previous_data) + 1), 1)
    return next_value


def extrapolateRetrieveData(datalist, number, yesterday=False):
    if yesterday:
        date = datetime.date.today() - datetime.timedelta(1)
        date_path = "data/{}".format(date)
        if not os.path.isdir(date_path):
            return False
    else:
        date = datetime.date.today()
        date_path = "data/{}".format(date)

    file = open("{}/{}.csv".format(date_path, datalist[0]), 'r')

    count = 0
    measurements = []

    # Take the last 30 measurements of the file, if there are less, take less.
    for line in reversed(list(file)):
        measurements.append(line.split(','))
        count += 1
        if count > 30:
            break
    file.close()

    # If the first element of the last element of the file is not equal to the station number, remnove that line.
    # This way the header of the csv file doesnt mess up the calculations if there are less then 30 measurements.
    if measurements[-1][0] != datalist[0]:
        del measurements[-1]

    # Add the measurements of the missing value to a list.
    measurementlist = []
    for measurement in measurements:
        measurementlist.append(round(float(measurement[number].strip('\n')), 2))

    # The data is added backwards to the list, so the list has to be turned around to predict the next value.
    measurementlist = measurementlist[::-1]

    return measurementlist


if __name__ == '__main__':
    weatherServer()
