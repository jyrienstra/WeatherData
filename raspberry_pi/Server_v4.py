import sys
import socket
from threading import Thread
from multiprocessing import Process
from re import findall
import datetime


def weather_server(processes, threads):
    """
    The initial function, the port and address can be set.
    :param processes: The amount of processes that should be runned
    :param threads: The amount of threads that should be runned
    """

    # If a parameter is set, use the given parameter. Else use the default port.
    if len(sys.argv) != 2:
        port = 7789
    else:
        port = int(sys.argv[1])

    # The ip the server listens on, 0.0.0.0 listens to all incoming connections.
    ip = "0.0.0.0"

    # Open a socket server
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

    # Open the filter file, to retrieve the stations to filter. Add the information to a list.
    filter_file = open('data/filter.csv')
    filter_list = [line.strip('\n') for line in filter_file]

    # Try to bind the port, when it fails, stop the program
    try:
        server_socket.bind((ip, port))
        print("Listening for connections on port {}".format(port))
    except socket.error as msg:
        print("Failed to bind port {}.".format(port))
        sys.exit()

    # Start listening to the socket
    server_socket.listen(5)

    # A check given to all processes, when this check fails, the process or threads gets killed
    check = True

    # Create N processes
    for i in range(processes):
        Process(target=station_process, args=(threads, server_socket, check, filter_list)).start()


def station_process(threads, server_socket, check, filter_list):
    i = 0
    while check:
        while i < threads:
            connection, address = server_socket.accept()
            ip, port = str(address[0]), str(address[1])
            print("Connected on {}:{}".format(ip, port))
            try:
                Thread(target=station_thread, args=(connection, ip, port, filter_list)).start()
                i += 1
            except:
                import traceback
                traceback.print_exc()
                i -= 1
    server_socket.close()


def station_thread(connection, ip, port, filter_list, MAX_BUFFER_SIZE=4096):
    """
    A thread function for every connection made. In this function the incoming data gets parsed and saved to the server.
    :param connection: The connection to the client
    :param ip: The ip the connection is coming from
    :param port: The port used by the connection
    :param filter_list: A list with all stations to save
    :param MAX_BUFFER_SIZE: The max size of the buffer for the socket
    """

    # Check is a variable that keeps the while loop running.
    check = True

    # A empty string for the initial incoming data, since the new data gets concatenated to the variable.
    data = ""

    # A loop that runs until the variable check gets False.
    while check:
        # Try to receive data, incase this fails, go to except.
        try:
            # The new data gets retrieved from the open connection.
            new_data = connection.recv(MAX_BUFFER_SIZE)

            # Decode the data from a byte string, to a ordinary string.
            data += new_data.decode()

            # If the begin tag of a xml file is in the incoming data stream.
            if "<?xml version=\"1.0\"?>" in data:
                # If the end tag of the needed information is in the incoming data stream.
                if "</WEATHERDATA>" in data:
                    # Extract the needed data using the extract_data function.
                    data_set, data = extract_data(data, filter_list)
                    # For all the measurements in the given data_set.
                    for measurement in data_set:
                        temperature = measurement[3]
                        temperatureDewpoint = measurement[4]

                        # Try to calculate the humidity, when data is missing fails go to except.
                        try:
                            humidity = str(round((
                                100 * ((112 - (0.1 * float(temperature)) + float(temperatureDewpoint)) / (
                                    112 + (0.9 * float(temperature)))) ** 8), 2))

                        # Incase the humidity calculation fails, run this except statement.
                        except ValueError:
                            # Set the humidity to -1, the missing values could be calculated using the extrapolate
                            # function.
                            humidity = -1
                        # Add the humidity to the measurement list.
                        measurement.append(humidity)
                        # Write the measurement list to a file using the write_to_file function.
                        write_to_file(measurement)
            # When there is no new incoming data, stop the loop.
            elif not data:
                check = False

        # When a ConnectionResetError is raised, print the msg and stop the loop.
        except ConnectionResetError as msg:
            print(msg)
            check = False

    # Close the connection and inform the user (of the server)
    connection.close()
    print("Connection {}:{} is terminated".format(ip, port))


def extract_data(data, filter_list):
    """
    Extract data from a given raw xml string.
    :param data: A raw XML string
    :param filter_list: A list with station numbers that should be filtered
    :return: A nested list with measurements and the remaining string of the raw XML string (incase of overflow)
    """

    # Partition the raw XML string using the start and end tags.
    first, end_tag, buffer = data.partition('<WEATHERDATA>\n')
    first, end_tag, buffer = buffer.partition('</WEATHERDATA>\n')
    usuable_data = first + end_tag

    # Create a empty list for all the data.
    data_set = []

    # For all 10 measurents in the raw XML string, run this loop.
    for i in range(10):
        # Create a empty list for the measurements
        data_list = []

        # Partition the string usin the end tag.
        first, end_tag, last = usuable_data.partition("</MEASUREMENT>\n")
        # Strip all the unnecessary garbage from the string.
        measurement = ((first + end_tag).replace("\t", "")).split("\n")
        # Throw away useless tags.
        measurement = measurement[1:-8]
        # For all the measurements in the measurementlist.
        for measure in measurement:
            # Extract the measurement from the XML-tags and append it to a new list.
            extracted_measure = (findall(r'>(.*?)<', measure))[0]
            data_list.append(extracted_measure)
        # If the measurement has the correct length.
        if len(data_list) == 8:
            # Check whether the station number is in the filter list, if so; append it to a list.
            if data_list[0] in filter_list:
                data_set.append(data_list)
        # When the measurement length is not 8, print the data for debugging purposes
        else:
            print(data)
        # Incase there is remaining data, add that to usuable_data. (There should be none)
        usuable_data = last

    return data_set, buffer


def write_to_file(data_list):
    """
    A function that writes a given data list to a station csv file
    :param data_list: A list with measurements to write
    """
    date = datetime.date.today()
    date_path = "data/{}".format(date)

    csv_file = open("{}/{}.csv".format(date_path, data_list[0]), 'a')
    csv_file.write(
        "{},{},{},{},{},{}\n".format(data_list[1], data_list[2], data_list[3], data_list[4],
                                     data_list[7],
                                     data_list[8]))

    csv_file.close()


if __name__ == '__main__':
    # Run the weather_server using P processes and T threads.
    P = 8
    T = 100
    weather_server(P, T)
