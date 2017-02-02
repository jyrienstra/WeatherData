// File Name GreetingServer.java
import java.net.*;
import java.io.*;
import java.lang.String;
import java.net.Socket;
import java.io.File;
import java.io.PrintWriter;
import java.text.SimpleDateFormat;
import java.util.Date;



public class NewConnection extends Thread{
    private ServerSocket serverSocket;
    private Socket server;
    private String date;

    public NewConnection(Socket server){
        this.server = server;
        Date systemDate = new Date();
        this.date = new SimpleDateFormat("yyyy-MM-dd").format(systemDate);
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

        return humidity;
    }

    public void writeToFile(String stn, String date, String time, String temp, String dewp, String visib, String humid) {
        String fileName = "data/" + date + "/" + stn + ".csv";

        //check if file exists so it doens't create random files that we don't need
        File file = new File(fileName);
        if(file.exists()) {
            try {
                System.out.println(fileName);
                FileWriter fw = new FileWriter(fileName, true);
                BufferedWriter bw = new BufferedWriter(fw);


                String row = date + "," + time + ","  + temp + "," + dewp + "," + visib + "," + humid + "\n";
                bw.write(row);
                bw.close();
                System.out.println("done!");
            } catch (IOException e) {
                e.printStackTrace();
            }
        }
    }

    public void run() {
        //int header = 0;
        try {
            System.out.println("Just connected to " + server.getRemoteSocketAddress()); //debugging
            BufferedReader in = new BufferedReader(new InputStreamReader(server.getInputStream())); //create bufferedreader

            String dataLine;

            int read = -1;
            String stn = "";
            String date = "";
            String time = "";
            String temperature = "";
            String dewpoint = "";
            String visibility= "";

            while((dataLine = in.readLine()) != null) { //readline reads 1 lime at the time checks if the buffer is not null
                //System.out.println(dataLine);
                if(dataLine.contains("<MEASUREMENT>")){
                    read = 1;
                }
                if(dataLine.contains("</MEASUREMENT>")){
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
                }

                if(read == 0){
                    //get the humidity value
                    double humidity = calculateHumidity(Float.parseFloat(temperature), Float.parseFloat(dewpoint));

                    //write file
                    writeToFile(stn, date, time, temperature, dewpoint, visibility, String.valueOf(humidity));
//                WriteFile test = new WriteFile(stn, date, time, temperature, dewpoint, visibility, String.valueOf(humidity));
//                test.writeToFile();
                }
            }

            server.close();
        }catch(IOException e) {
            e.printStackTrace();
        }
    }

}