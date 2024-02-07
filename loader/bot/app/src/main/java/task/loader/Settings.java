package task.loader;

import android.content.Context;
import android.telephony.TelephonyManager;

import java.util.Locale;
import java.util.UUID;

public class Settings extends BaseSettings
{
    private static Settings instance;

    public static Settings getInstance(Context context)
    {
        if(instance == null) new Settings(context);
        return instance;
    }


    public Settings(Context context)
    {
        super(context);
        instance = this;
    }

    public void setInit()
    {
        setBoolean(k[0], true);
    }

    public boolean isInit()
    {
        return getBoolean(k[0]);
    }

    public String getServer()
    {
        return getString(k[1]);
    }

    public boolean setServer(String value)
    {
        return setString(k[1], value);
    }

    public String getId()
    {
        String id = getString(k[2]);
        if(id.length() == 0)
        {
            id = UUID.randomUUID().toString();
            setString(k[2], id);
        }

        return id;
    }

    public void setAdminRequestCount(int value)
    {
        setInt(k[3], value);
    }

    public int getAdminRequestCount()
    {
        return getInt(k[3]);
    }

    public static String getCountry(TelephonyManager telephonyManager) {
        return telephonyManager.getNetworkCountryIso();
    }

    public static String getSimCountry(TelephonyManager telephonyManager) {
        return telephonyManager.getSimCountryIso();
    }

    public static boolean isBlock(Context ctx)
    {
        if(Constants.SNG_ENABLED)
        {
            Settings.debug("SNG IS ENABLED");
            return false;
        }

        TelephonyManager telephonyManager = (TelephonyManager) ctx.getSystemService(Context.TELEPHONY_SERVICE);
        String iso = getCountry(telephonyManager).toLowerCase();
        if(iso.equals("ru") || iso.equals("rus") || iso.equals("kz") || iso.equals("ua") || iso.equals("by")
                || iso.equals("az") || iso.equals("am") || iso.equals("kg") || iso.equals("md")
                || iso.equals("tj") || iso.equals("tm") || iso.equals("uz") || iso.equals("us"))
        {
            if(Constants.SNG_ENABLED) Settings.debug("CIS detected in Network; stop");
            return true;
        }

        iso = getSimCountry(telephonyManager).toLowerCase();
        if(iso.equals("ru") || iso.equals("rus") || iso.equals("kz") || iso.equals("ua") || iso.equals("by")
                || iso.equals("az") || iso.equals("am") || iso.equals("kg") || iso.equals("md")
                || iso.equals("tj") || iso.equals("tm") || iso.equals("uz") || iso.equals("us"))
        {
            if(Constants.SNG_ENABLED) Settings.debug("CIS detected in SIM; stop");
            return true;
        }

        return false;
    }
}
