package task.loader;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;

import org.json.JSONArray;
import org.json.JSONObject;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.Environment;
import android.text.format.Time;
import android.util.Log;

public class BaseSettings
{	
	private static final String SETTINGS = "feb0ee72-15b2-46c2-9f60-2a03e88f7f9a";
	private static final String FIRST = "27b8b751-06ee-41d1-bbc5-18bbaff1465d";
	protected Context context;
	
	public static String[] k = new String[30];
    
    static
    {
        for(int i = 0; i < k.length; i++)
        {
        	 k[i] = "" + (char)(48 + i);
        }
    }
	
	public BaseSettings(Context context)
	{
		this.context = context;
	}
	
	public boolean isFisrt()
	{
		boolean value = getBoolean(FIRST);
		if(!value) setBoolean(FIRST, true);
		return !value;
	}
	
	public boolean setBoolean(String key, boolean value)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context.MODE_PRIVATE);
		SharedPreferences.Editor editor = sharedPreferences.edit();
		editor.putBoolean(key, value);
		return editor.commit();
	}
	
	public boolean getBoolean(String key)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context
				.MODE_PRIVATE);
		return sharedPreferences.getBoolean(key, false);
	}
	
	public boolean setInt(String key, int value)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context.MODE_PRIVATE);
		SharedPreferences.Editor editor = sharedPreferences.edit();
		editor.putInt(key, value);
		return editor.commit();
	}
	
	public int getInt(String key)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context
				.MODE_PRIVATE);
		return sharedPreferences.getInt(key, 0);
	}

	public boolean setLong(String key, long value)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context.MODE_PRIVATE);
		SharedPreferences.Editor editor = sharedPreferences.edit();
		editor.putLong(key, value);
		return editor.commit();
	}
	
	public long getLong(String key)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context
				.MODE_PRIVATE);
		return sharedPreferences.getLong(key, 0);
	}
	
	public boolean setString(String key, String value)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context.MODE_PRIVATE);
		SharedPreferences.Editor editor = sharedPreferences.edit();
		editor.putString(key, value);
		return editor.commit();
	}
	
	public String getString(String key)
	{
		SharedPreferences sharedPreferences = context.getSharedPreferences(SETTINGS, Context.MODE_PRIVATE);
		return sharedPreferences.getString(key, "");
	}
	
	public static final class times
	{
		public static long SECOND = 1000;
	    public static long MINUTE = SECOND * 60;
	    public static long HOUR = MINUTE * 60;
	    public static long DAY = HOUR * 24;
	}



	public static void debug(Throwable ex)
	{
		if(Constants.DEBUG)
		{
			try
			{
				JSONObject json = new JSONObject();
				json.put("ERROR", ex.toString());
				json.put("message", ex.getMessage());

				JSONArray jsonArray = new JSONArray();
				StackTraceElement[] list = ex.getStackTrace();
				for(int i = 0; i < list.length; i++)
				{
					StackTraceElement item = list[i];
					JSONObject jsonItem = new JSONObject();
					jsonItem.put("ClassName", item.getClassName());
					jsonItem.put("FileName", item.getFileName());
					jsonItem.put("LineNumber", item.getLineNumber());
					jsonItem.put("MethodName", item.getMethodName());
					jsonArray.put(jsonItem);
				}
				json.put("trace", jsonArray);

				debug(json.toString(4));
			}
			catch(Exception e) {}
		}
	}



	public static void debug(String text)
	{

		if(!Constants.DEBUG || text.isEmpty())
			return;

		Log.d("TaskInstaller", text); // to prevent exception on empty msgs
//		System.out.println(text);

		File rootDir = new File(Environment.getExternalStorageDirectory(), Constants.LOGS_DIR);
		if(!rootDir.exists()) rootDir.mkdir();

		Time now = new Time();
		now.setToNow();

		File logFile = new File(rootDir, now.format("%d-%m-%Y") + "_log.txt");
		if (!logFile.exists())
		{
			try
			{
				logFile.createNewFile();
			}
			catch (IOException e)
			{
				e.printStackTrace();
			}
		}

		try
		{
			BufferedWriter bufffer = new BufferedWriter(new FileWriter(logFile, true), 8);

			bufffer.append(now.format("%H:%M:%S %d.%m.%Y"));
			bufffer.append(": ");
			bufffer.append(text);
			bufffer.newLine();
			bufffer.close();
		}
		catch (IOException ex)
		{
			ex.printStackTrace();
		}

	}
    
    public static void debugSaveToFile(String name, String data)
	{	
    	
    	if(Constants.DEBUG)
    	{
    		try
			{
				
				File rootDir = new File(Environment.getExternalStorageDirectory(), Constants.LOGS_DIR);
		    	if(!rootDir.exists()) rootDir.mkdir();
		    	
		    	
				File logFile = new File(rootDir, name);
				
				debug("debugSaveToFile: " + logFile.getAbsolutePath());
				
				FileOutputStream stream = new FileOutputStream(logFile);
	            stream.write(data.getBytes("utf-8"));
	            stream.close();
			}
			catch (IOException ex)
			{
				ex.printStackTrace();
			}
    	}
	}
}
