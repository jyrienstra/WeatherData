// File Name GreetingServer.java
import java.net.*;
import java.io.*;
import java.lang.String;
import org.xml.sax.*;
import org.w3c.dom.*;
import javax.xml.parsers.*;
import java.io.*;
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
        serverSocket.setSoTimeout(10000);
    }

    //splits a string beginning with ">" and ending with "</"
    public String splitString(String s){
        int start = -1;
        int end = -1;
        start = s.indexOf(">") + 1;
        end = s.indexOf("</");
        s = s.substring(start,end);
        return s;
    }

    public double calculateHumidity(float temp, float dewpoint){
        double humidity = -1;
        humidity = (100*(112-(0.1*temp) + dewpoint)) / (112+(0.9*temp));
        humidity = Math.pow(humidity,8);

//        humidity = str(round((
//                100 * ((112 - (0.1 * float(temperature)) + float(temperatureDewpoint)) / (
//                112 + (0.9 * float(temperature)))) ** 8), 2))
        return humidity;
    }

    public void run() {
        int header = 0;
        while(true) {
            try {

                System.out.println("Waiting for client on port " + serverSocket.getLocalPort() + "..."); //pure debugging does nothing
                Socket server = serverSocket.accept(); //check for connection then execute code below



                System.out.println("Just connected to " + server.getRemoteSocketAddress()); //debugging
                BufferedReader in = new BufferedReader(new InputStreamReader(server.getInputStream())); //create bufferedreader

                String dataLine;
                ArrayList<String> test = new ArrayList<String>();

                int read = -1;
                String stn = "";
                String date = "";
                String time = "";
                String temperature ="";
                String dewpoint = "";
                String visibility="";

                while((dataLine = in.readLine()) != null) { //readline reads 1 lime at the time
                    if(dataLine.contains("<WEATHERDATA>")){
                        read = 1;
                    }
                    if(dataLine.contains("</WEATHERDATA>")){
                        read = 0;
                    }


                    if(read == 1){
                        if(dataLine.contains("<STN>")){
                            stn = splitString(dataLine); //split the dataline to get the data without the tags
                        }
                        if(dataLine.contains("<DATE>")){
                            date = splitString(dataLine);
                        }
                        if(dataLine.contains("<TIME>")){
                            time = splitString(dataLine);
                        }
                        if(dataLine.contains("<TEMP>")){
                            temperature = splitString(dataLine);
                        }
                        if(dataLine.contains("<DEWP>")){
                            dewpoint = splitString(dataLine);
                        }
                        if(dataLine.contains("<VISIB>")){
                            visibility = splitString(dataLine);
                        }

                        //debugging
//                        System.out.println(stn);
//                        System.out.println(date);

                    }

                    if(read == 0){
                        PrintWriter pw = new PrintWriter(new FileOutputStream("test2.csv",true));
                        StringBuilder sb = new StringBuilder();
                        if(header == 0){
                            pw.write("stn, date, time, temperature, dewpoint, visibility, humidity\n");
                            header = 1;
                        }


                        //get the humidity value
                        double humidty = calculateHumidity(Float.parseFloat(temperature), Float.parseFloat(dewpoint));



                        String row = stn + "," + date + "," + time + "," + "," + temperature + "," + dewpoint + "," + visibility + "," + humidty + "\n";
                        pw.write(row);
                        pw.close();
                        System.out.println("done!");
                    }
                }


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