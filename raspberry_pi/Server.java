// File Name GreetingServer.java
import java.net.*;
import java.io.*;
import java.lang.String;

public class Server extends Thread {
    private ServerSocket serverSocket;

    public Server(int port) throws IOException {
        serverSocket = new ServerSocket(port);
        serverSocket.setSoTimeout(10000);
    }

    public void run() {
        while(true) {
            try {
                System.out.println("Waiting for client on port " +
                        serverSocket.getLocalPort() + "...");
                Socket server = serverSocket.accept();

                System.out.println("Just connected to " + server.getRemoteSocketAddress());
                DataInputStream in = new DataInputStream(server.getInputStream());


                System.out.println(in.readUTF());
                DataOutputStream out = new DataOutputStream(server.getOutputStream());
               // out.writeUTF("Thank you for connecting to " + server.getLocalSocketAddress()
                        //+ "\nGoodbye!");

                String test = "test";
                out.writeUTF(test);
                String test2 = "test";
                out.writeUTF(test);
                server.close();

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