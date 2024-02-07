package task.loader;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;

public class ShellCommand
{
    private String mCommand;
    ByteArrayOutputStream mBuffer = new ByteArrayOutputStream();

    public ShellCommand(String command)
    {
        mCommand = command;
    }

    public boolean execute()
    {
        try
        {
            Process process = Runtime.getRuntime().exec(mCommand);
            //DataOutputStream outputStream = new DataOutputStream(process.getOutputStream());
            InputStream inputStream = process.getInputStream();


            byte[] data = new byte[1024];

            while (true)
            {
                int length = inputStream.read(data);
                if(length == -1) break;
                mBuffer.write(data, 0, length);
            }
            
            process.waitFor();

            return true;
        }
        catch (Exception ex)
        {
            
        }

        return false;
    }

    public String getOutput()
    {

        try
        {
            return mBuffer.toString("utf-8");
        }
        catch (UnsupportedEncodingException e)
        {
            e.printStackTrace();
        }

        return null;
    }
}
