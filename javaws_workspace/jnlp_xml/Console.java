import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.io.*;
import java.util.concurrent.CopyOnWriteArraySet;
import java.lang.reflect.*;
import javax.swing.border.*;

/**
 *
 * @author Alistair
 */
public class Console extends JPanel implements ThreadCompleteListener{// implements KeyListener {
    
    private static final long serialVersionUID = -4538532229007904362L;
    private JLabel keyLabel;
    private String prompt = "_";
    private LabelStreamer jls;
    private String oldTxt = "";   //keep track of last user input text state
    private JScrollPane scrollPane;
    
    public static void main(String[] args){
        final String className;
        final String[] givenArgs;
        if (args.length >0){
            className = args[0];
            givenArgs = Arrays.copyOfRange(args, 1, args.length);

            EventQueue.invokeLater(new Runnable(){
                public void run(){
                    try{
                        Console console = new Console(className, givenArgs);
                        JFrame frame = new JFrame();
                        
                        //frame.setLayout(new BorderLayout());
                        frame.add(console);//;, BorderLayout.CENTER);
                        
                        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
                        frame.setVisible(true);
                        frame.setSize(400,400);
                        frame.setLocation(40,40);

                     }catch(Exception e){
                        e.printStackTrace();
                     }
                }
            });
        }
        else{
            System.err.println("No argument given for class to run... Nothing to do.");
        }
    }

    public Console(final String className, final String args[]) {
        super();
        setSize(300, 200);
        setLayout(new BorderLayout());
        //setLayout();


        keyLabel = new JLabel("<HTML>"+this.prompt+"</HTML>", JLabel.LEFT);
        keyLabel.setVerticalAlignment(JLabel.TOP);
        Border empty = new EmptyBorder(5, 5, 5, 5);
        keyLabel.setBorder(empty);
        jls = new LabelStreamer(keyLabel,this);
        setFocusable(true);
        keyLabel.setFocusable(true);
        keyLabel.addKeyListener(jls);
        addKeyListener(jls);
        scrollPane = new JScrollPane(keyLabel);
        add(scrollPane, BorderLayout.CENTER);
        setVisible(true);
        System.setIn(jls);
        redirectSystemStreams();

        NotifyingThread thread1 = new NotifyingThread(){
            public void doRun(){
                try{
                    Object[] argObj = new Object[1];
                    argObj[0] = args;

                    Class<?> c = Class.forName(className);
                    Method m = c.getDeclaredMethod("main", args.getClass());
                    m.invoke(null, argObj);

                }catch(Exception e){
                    e.printStackTrace();
                }
            }
        };

        thread1.addListener(this); // add ourselves (console) as a listener
        thread1.start();           // Start the Thread
    }
    
    public void notifyOfThreadComplete(Thread thread){
        //stop accepting keyboard input
        System.out.println();
        //System.out.println("----- EXECUTION ENDED -----");

        removePrompt();

        jls.ReadOnly = true;
    }

    public void scrollToBottom(){
        JScrollBar vertical = scrollPane.getVerticalScrollBar();
        vertical.setValue(vertical.getMaximum());
    }

    public void clearOldTxt(){
        this.oldTxt = "";
    }

    public String getPrompt() {
        return this.prompt;
    }
    
    public void setPrompt(String s) {
        this.prompt = s;
    }
    
    public void print() {
        //System.err.println("before print: "+this.keyLabel.getText());

        String currentText = this.keyLabel.getText();
        currentText = currentText.substring("<HTML>".length(), currentText.length()-(this.oldTxt+this.prompt+"</HTML>").length());
        this.keyLabel.setText("<HTML>" + currentText + jls.getVector().toString() + this.prompt+"</HTML>");
        this.repaint();
        this.oldTxt = jls.getVector().toString();
        //System.err.println("after print: "+this.keyLabel.getText());
        scrollToBottom();
    }
    
    @SuppressWarnings("unchecked")
    public void print(String s) {
        //System.err.println("print (string) err: "+s);
        jls.getVector().add(s);
        this.print();
    }

    public void printOutput(String s, boolean isErrStream){
        //System.err.println("before print output: "+this.keyLabel.getText());
        String color = (isErrStream) ? "red" : "blue";

        String currentText = this.keyLabel.getText();
        currentText = currentText.substring("<HTML>".length(), currentText.length()-(this.prompt+"</HTML>").length());
        s = s.replace(">","&gt;");
        s = s.replace("<","&lt;");
        s = s.replace(System.getProperty("line.separator"),"<BR>");
        this.keyLabel.setText("<HTML>"+currentText+"<font color='"+color+"'>"+s+"</font>"+this.prompt+"</HTML>");
        this.repaint();
        //System.err.println("after print output: "+this.keyLabel.getText());
        scrollToBottom();
    }

    public void removePrompt(){
        String currentText = this.keyLabel.getText();
        currentText = currentText.substring("<HTML>".length(), currentText.length()-(this.prompt+"</HTML>").length());
        //System.err.println(currentText);
        this.keyLabel.setText("<HTML>"+currentText+"</HTML>");

        this.prompt = "";
    }
        
    private void redirectSystemStreams() {
        ConsoleOutputStream out = new ConsoleOutputStream(this, false);
        ConsoleOutputStream err = new ConsoleOutputStream(this, true);
        System.setOut(new PrintStream(out, true));
        System.setErr(new PrintStream(err, true));
    }
}

class LabelStreamer extends InputStream implements KeyListener {
    private JLabel keyLabel;
    private String str = null;
    private int pos = 0;
    private String oldTxt = "";
    private ConsoleVector vec = new ConsoleVector();
    private Vector history = new Vector();
    private int history_index = -1;
    private boolean history_mode = false;
    public boolean ReadOnly = false;
    private Console c;
    
    public ConsoleVector getVector(){
        return this.vec;
    }
    public String getOldTxt(){
       return this.oldTxt;
    }
    public LabelStreamer(JLabel jlabel, Console c) {
        keyLabel = jlabel;
        this.c = c;
    }
    @Override
    public void keyTyped(KeyEvent e) {
    }
    
    @Override
    public void keyPressed(KeyEvent e) {
    }
    
    @Override
    public void keyReleased(KeyEvent e) {
        this.handleKey(e);
    
        synchronized (this) {
            //maybe this should only notify() as multiple threads may
            //be waiting for input and they would now race for input
            this.notifyAll();
        }
    }

    @Override
    public int read() {
        //System.err.println("hello form read");
        //test if the available input has reached its end
        //and the EOS should be returned
        if(str != null && pos == str.length()){
            str =null;
            //this is supposed to return -1 on "end of stream"
            //but I'm having a hard time locating the constant
            return java.io.StreamTokenizer.TT_EOF;
        }
        while (str == null || pos >= str.length()) {
            try {
                //according to the docs read() should block until new input is available
                synchronized (this) {
                    this.wait();
                    //System.err.println("waiting from read");
                }
            } catch (InterruptedException ex) {
                ex.printStackTrace();
            }
        }
        //read an additional character, return it and increment the index
        return str.charAt(pos++);
    }

    
    
    protected void handleKey(KeyEvent e) {
        int code = e.getKeyCode();
        char c = e.getKeyChar();
        if (!this.ReadOnly) {
            if (code == 38 | code == 40) {
                if (code == 38) {
                    history(1);
                } else if (code == 40 & this.history_mode != false) {
                    history(2);
                }
            } else {
                this.history_index = -1;
                this.history_mode = false;
                if (code == 13 || code == 10) {
                    enter();
                } else if (code == 8) {
                    this.backspace();
                } else {
                    if (c != KeyEvent.CHAR_UNDEFINED) {
                        this.c.print(String.valueOf(c));
                    }
                }
            }
        }
    }
    
    private void backspace() {
        if (!this.vec.isEmpty()) {
            this.vec.remove(this.vec.size() - 1);
            this.c.print();
        }
    }
    
    @SuppressWarnings("unchecked")
    private void enter() {
        String com = this.vec.toString();
        
        this.history.add(com);
        this.vec.clear();
        this.c.clearOldTxt();
        // <HTML> </HTML>
        String currentText = this.keyLabel.getText();
        currentText = currentText.substring("<HTML>".length(), currentText.length()-(this.c.getPrompt()+"</HTML>").length());
        this.keyLabel.setText("<HTML>" +currentText+"<BR>"+this.c.getPrompt() +"</HTML>");
        this.str = com+System.getProperty("line.separator");   //add the command to the buffer
    }
    
    private void history(int dir) {
        if (this.history.isEmpty()) {
            return;
        }

        if (dir == 1) {
            this.history_mode = true;
            this.history_index++;
            if (this.history_index > this.history.size() - 1) {
                this.history_index = 0;
            }
            // System.out.println(this.history_index);
            this.vec.clear();
            String p = (String) this.history.get(this.history_index);
            this.vec.fromString(p.split(""));
            
        } else if (dir == 2) {
            this.history_index--;
            if (this.history_index < 0) {
                this.history_index = this.history.size() - 1;
            }
            // System.out.println(this.history_index);
            this.vec.clear();
            String p = (String) this.history.get(this.history_index);
            this.vec.fromString(p.split(""));
        }
        
        this.c.print();
    }
}

class ConsoleVector extends Vector {
    private static final long serialVersionUID = -5527403654365278223L;

    @SuppressWarnings("unchecked")
    public void fromString(String[] p) {
        for (int i = 0; i < p.length; i++) {
            this.add(p[i]);
        }
    }
    
    public ConsoleVector() {
        super();
    }
    
    @Override
    public String toString() {
        StringBuffer s = new StringBuffer();
        for (int i = 0; i < this.size(); i++) {
            s.append(this.get(i));
        }
        return s.toString();
    }
}

 class ConsoleOutputStream extends OutputStream {
    private boolean isErrStream = false;
    private Console c;

    public ConsoleOutputStream(Console c, boolean isErrStream){
        super();
        this.isErrStream = isErrStream;
        this.c = c;
    }

    public boolean isErrStream(){
        return isErrStream;
    }
    @Override
    public void write(final int b) throws IOException {
        //System.err.print(b);
        c.printOutput(String.valueOf((char) b),isErrStream);
    }
    
    @Override
    public void write(byte[] b, int off, int len) throws IOException {
        c.printOutput(new String(b, off, len),isErrStream);
        //System.err.print(new String(b, off, len));
    }
    
    @Override
    public void write(byte[] b) throws IOException {
        c.printOutput(new String(b, 0, b.length), isErrStream);
        //System.err.print(new String(b, 0, b.length));
    }
}

interface ThreadCompleteListener {
    void notifyOfThreadComplete(final Thread thread);
}

abstract class NotifyingThread extends Thread {
    private final Set<ThreadCompleteListener> listeners = new CopyOnWriteArraySet<ThreadCompleteListener>();
    public final void addListener(final ThreadCompleteListener listener) {
        listeners.add(listener);    
    }
    public final void removeListener(final ThreadCompleteListener listener) {
        listeners.remove(listener);
    }
    private final void notifyListeners() {
        for (ThreadCompleteListener listener : listeners) {
            listener.notifyOfThreadComplete(this);
        }
    }
    @Override
    public final void run() {
        try {
            doRun();
        } finally {
            notifyListeners();
        }
    }
    public abstract void doRun();
}

