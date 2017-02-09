// File Name GreetingServer.java
import java.net.*;
import java.io.*;
import java.lang.String;
import java.net.Socket;
import java.io.File;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;



public class Server extends Thread {
    private ServerSocket serverSocket;
    private static File file;

    //Create a threadpool that is 10 threads big
    ExecutorService threadPool = Executors.newFixedThreadPool(10);


    public Server(int port) throws IOException {
        serverSocket = new ServerSocket(port);
        serverSocket.setSoTimeout(0); //A timeout of zero is interpreted as an infinite timeout.
    }

    public void run() {
        while(true) {
            try {
                System.out.println("Waiting for client on port " + serverSocket.getLocalPort() + "..."); //pure debugging does nothing
                Socket server = serverSocket.accept(); //check for connection then execute code below

                //Start a new thread in the threadpool for each connecction
                threadPool.submit(new NewConnection(server));

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
        int port = Integer.parseInt(args[0]);
        try {
            Thread t = new Server(port);
            t.start();
        }catch(IOException e) {
            e.printStackTrace();
        }
    }
}