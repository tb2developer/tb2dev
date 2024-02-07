package task.loader.db;

import android.content.ContentValues;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;

import task.loader.Constants;
import task.loader.Settings;

public class TableTasks
{
    private MainDb mMainDb;

    public static final String TABLE_NAME = "tasks";

    public static final class COLUMNS
    {
        public static final String ID = "_id";
        public static final String TRY_COUNT = "try_count";
        public static final String PACKAGE = "package";
        public static final String PATH = "path";
        public static final String TASK_ID = "task_id";
    }

    public static final String CREATE_TABLE = String.format("create table %s ( %s integer primary key autoincrement, %s integer, %s TEXT, %s TEXT, %s TEXT)", TABLE_NAME,
            COLUMNS.ID,
            COLUMNS.TRY_COUNT,
            COLUMNS.PACKAGE,
            COLUMNS.PATH,
            COLUMNS.TASK_ID

    );

    public TableTasks(MainDb mainDb)
    {
        mMainDb = mainDb;
    }

    public boolean insert(Task item)
    {
        if(Constants.DEBUG) Settings.debug("TableTasks::insert() item: " + item);

        try
        {
            SQLiteDatabase db = mMainDb.getWritableDatabase();
            ContentValues cv = new ContentValues();
            cv.put(COLUMNS.TRY_COUNT, item.getTryCount());
            cv.put(COLUMNS.PACKAGE, item.getPackageName());
            cv.put(COLUMNS.PATH, item.getPath());
            cv.put(COLUMNS.TASK_ID, item.getTaskId());
            db.insert(TABLE_NAME, null, cv);
            db.close();

            return true;
        }
        catch(Exception ex)
        {
            ex.printStackTrace();
        }
        return false;
    }

    public Task getNext()
    {
        Task item = null;

        SQLiteDatabase db = mMainDb.getReadableDatabase();

        Cursor cursor = db.query(TABLE_NAME, getColumns(), null, null, null, null, COLUMNS.ID + " ASC", null);
        if (cursor != null)
        {
            if(cursor.moveToFirst())
            {
                if(Constants.DEBUG) Settings.debug("TableTasks::getNext() id: " + cursor.getLong(0));

                item = loadItem(cursor);
            }

            cursor.close();
        }
        db.close();

        return item;
    }

    public Task getTaskByPackage(String packageName)
    {
        Task item = null;

        SQLiteDatabase db = mMainDb.getReadableDatabase();
        Cursor cursor = db.query(TABLE_NAME, getColumns(), COLUMNS.PACKAGE + " = ?", new String[] { packageName }, null, null, null);
        if (cursor != null)
        {
            if(cursor.moveToFirst())
            {
                if(Constants.DEBUG) Settings.debug("TableTasks::getTaskByPackage() id: " + cursor.getLong(0));

                item = loadItem(cursor);
            }

            cursor.close();
        }
        db.close();

        return item;
    }

    public Task[] getAll()
    {
        Task[] items = new Task[0];

        SQLiteDatabase db = mMainDb.getReadableDatabase();

        Cursor cursor = db.query(TABLE_NAME, getColumns(), null, null, null, null, COLUMNS.ID + "" +
                " ASC");
        if (cursor != null)
        {
            items = new Task[cursor.getCount()];
            if(Constants.DEBUG) Settings.debug("TableTasks::getAll() items.length: " + items.length);
            int i = 0;
            while(cursor.moveToNext())
            {
                items[i++] = loadItem(cursor);
            }

            cursor.close();
        }
        db.close();

        return items;
    }

    public int getCount()
    {
        int count = 0;

        SQLiteDatabase db = mMainDb.getReadableDatabase();

        Cursor cursor = db.rawQuery("select count(*) from " + TABLE_NAME, null);
        if (cursor != null)
        {
            cursor.moveToFirst();
            count = cursor.getInt(0);
            cursor.close();
        }
        db.close();

        return count;
    }

    public void remove(Task item)
    {
        if(Constants.DEBUG) Settings.debug("TableTasks::remove() item: " + item);

        SQLiteDatabase db = mMainDb.getWritableDatabase();
        db.delete(TABLE_NAME, COLUMNS.ID + " = ?", new String[]{String.valueOf(item.getId())});
        db.close();
    }


    public void updateTryCount(Task item, int tryCount)
    {
        if(Constants.DEBUG) Settings.debug(String.format("TableTasks::updateTryCount() id[%d], tryCount[%d]", item.getId(), tryCount));

        ContentValues cv = new ContentValues();
        cv.put(COLUMNS.TRY_COUNT, tryCount);

        SQLiteDatabase db = mMainDb.getWritableDatabase();
        db.update(TABLE_NAME, cv, COLUMNS.ID + " = ?", new String[]{String.valueOf(item.getId())});
        db.close();
    }

    private String[] getColumns()
    {
        return new String[] { COLUMNS.ID, COLUMNS.TRY_COUNT, COLUMNS.PACKAGE, COLUMNS.PATH, COLUMNS.TASK_ID};
    }

    private Task loadItem(Cursor cursor)
    {
        return new Task(
                cursor.getLong(0), // id
                cursor.getInt(1), // priority
                cursor.getString(2), // package
                cursor.getString(3), // path
                cursor.getString(4)); // task id
    }

    public void close()
    {
        try
        {
            mMainDb.close();
        }
        catch (Exception ex)
        {
            ex.printStackTrace();
        }
    }
}
