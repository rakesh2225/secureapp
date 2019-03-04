import java.net.*;
import java.io.*;
import java.util.*;

public class EchoServer {
    public static ThreadList threadlist = new ThreadList();
    public static void main(String[] args) throws IOException {
        System.out.println("TCP Server program by Rakesh for Lab2 - SecAd - Spring 2019");
        if (args.length != 1) {
            System.err.println("Usage: java EchoServer <port number>");
            System.exit(1);
        }
        int portNumber = Integer.parseInt(args[0]);
        ServerSocket serverSocket =
            new ServerSocket(Integer.parseInt(args[0]));
        ThreadList threadlist = new ThreadList();
        System.out.println("EchoServer is running at port " + Integer.parseInt(args[0]));
        while (true) {
            Socket clientSocket = serverSocket.accept(); 
            EchoServerThread newThread = new EchoServerThread(threadlist, clientSocket);
            threadlist.addThread(newThread);
            newThread.start();
        }
    }
}

class EchoServerThread extends Thread {
    private Socket clientSocket = null;
    private ThreadList threadlist = null;
    public EchoServerThread() {}
    public EchoServerThread(Socket clientSocket) {
        this.clientSocket = clientSocket;
    }
    public EchoServerThread(ThreadList threadlist, Socket clientSocket) {
        this.threadlist = threadlist;
        this.clientSocket = clientSocket;
    }
    public void run () {
        try {
            System.out.println("Created a new echo server thread.");
            System.out.println("A client is connected, total clients connected: " + threadlist.getNumberofThreads());    
            PrintWriter out =
                new PrintWriter(clientSocket.getOutputStream(), true);                   
            BufferedReader in = new BufferedReader(
                new InputStreamReader(clientSocket.getInputStream()));

            String inputLine;
            while ((inputLine = in.readLine()) != null) {
                System.out.println("received from client: " + inputLine);
                System.out.println("Echo back");
                out.println(inputLine);
            }
        } catch (IOException e) {
            System.out.println("Exception caught when trying to listening for a connection");
            System.out.println(e.getMessage());
        }
    }
}

class ThreadList {
    private List<EchoServerThread> threadlist = new ArrayList<EchoServerThread>(); 
    public ThreadList() {        
    }
    public synchronized int getNumberofThreads() {
        return threadlist.size();
    }
    public synchronized void addThread(EchoServerThread newthread) {
        threadlist.add(newthread);  
    }
    public void removeThread(EchoServerThread thread) {
        threadlist.remove(thread);      
    }
    public void sendToAll(String message) {
        
    }
}
