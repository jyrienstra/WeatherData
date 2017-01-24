import sys
import socket
from threading import Thread
import xml.etree.ElementTree as ET


def stationThread(connection, ip, port, MAX_BUFFER_SIZE=3500):
    check = True

    while check:
        print("test")
        # Receiving data
        data = connection.recv(MAX_BUFFER_SIZE)
        # if data is bigger then the MAX_BUFFER_SIZE, notify the user.
        size = sys.getsizeof(data)
        # Check the size of the data
        if size >= MAX_BUFFER_SIZE:
            print("Too many bytes, buffer too small: {}".format(size))
            break
        # When there is no data, stop the loop
        elif not data:
            break
        print(data)
        # Parse the data to a readable format, XML
        tree = ET.fromstring(data)

        # print(prettify(tree))

        query = ""

        # Create a query for every station in the XML file
        for parent in tree.findall('MEASUREMENT'):
            query = "INSERT INTO `database`.`measurements` (`STN`, `DATE`, `TIME`, `TEMP`, `DEWP`, `STP`, `SLP`, `VISIB`, `WDSP`, `PRCP`, `SNDP`, `FRSHTT`, `CLDC`, `WNDDIR`) VALUES ({}, {} ,{} ,{} ,{}, {}, {}, {}, {}, {} ,{} ,{} ,{} ,{});".format(
                parent.find('STN').text, parent.find('DATE').text, parent.find('TIME').text, parent.find('TEMP').text,
                parent.find('DEWP').text, parent.find('STP').text, parent.find('SLP').text, parent.find('VISIB').text,
                parent.find('WDSP').text, parent.find('PRCP').text, parent.find('SNDP').text,
                parent.find('FRSHTT').text, parent.find('CLDC').text, parent.find('WNDDIR').text)

            # print(query)

        # Return the query to the client
        connection.sendall(query.encode())

    # Close the connection, when the loop stops
    connection.close()
    print("Connection {}:{} is terminated".format(ip, port))


def weatherServer():
    port = 7789
    ip = "localhost"

    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

    # Try to bind the port, when it fails, stop the program
    try:
        server_socket.bind((ip, port))
        print("Listening for connections on port {}".format(port))
    except socket.error as msg:
        print("Failed to bind port {}.".format(port))
        import sys
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


if __name__ == '__main__':
    weatherServer()
