package task.loader.db;


public class Task
{
	private long id; 
	private int tryCount;
	private String path;
	private String packageName;
	private String taskId;
	
	public Task(long id, int tryCount, String packageName, String path, String taskId)
	{
		this.id = id;
		this.tryCount = tryCount;
		this.packageName = packageName;
		this.path = path;
		this.taskId = taskId;
	}

	public Task(int tryCount, String packageName, String path, String taskId)
	{
		this.id = 0;
		this.tryCount = tryCount;
		this.packageName = packageName;
		this.path = path;
		this.taskId = taskId;
	}
	
	public long getId()
	{
		return id;
	}
	

	public String toString()
	{
		return String.format("%d: %d|%s|%s|%s",  id, tryCount, taskId, packageName, path);
	}

	public int getTryCount()
	{
		return tryCount;
	}

	public String getPath()
	{
		return path;
	}

	public String getPackageName()
	{
		return packageName;
	}

	public String getTaskId()
	{
		return taskId;
	}
}
