// File Name GreetingServer.java
import java.net.*;
import java.io.*;
import java.lang.String;
import org.xml.sax.*;
import org.w3c.dom.*;
import javax.xml.parsers.*;
import java.io.*;
import java.net.Socket;
import java.util.*;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.PrintWriter;



public class Server extends Thread {
    private ServerSocket serverSocket;
    private static File file;
    private static String fileName;


    public Server(int port) throws IOException {
        serverSocket = new ServerSocket(port);
        serverSocket.setSoTimeout(0); //A timeout of zero is interpreted as an infinite timeout.
    }

    public void run() {
        while(true) {
            try {
                System.out.println("Waiting for client on port " + serverSocket.getLocalPort() + "..."); //pure debugging does nothing
                Socket server = serverSocket.accept(); //check for connection then execute code below


                //start new thread for each connection
                Thread t2 = new NewConnection(server);
                t2.start();

            }catch(SocketTimeoutException s) {
                System.out.println("Socket timed out!");
                break;
            }catch(IOException e) {
                e.printStackTrace();
                break;
            }
        }
    }

    public static void main(String [] args) {
        String fileName = "test2.csv";
        file = new File(fileName);

        int port = Integer.parseInt(args[0]);
        try {
            Thread t = new Server(port);
            t.start();
        }catch(IOException e) {
            e.printStackTrace();
        }
    }
}