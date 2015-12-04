package com.android.localdatabase;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.util.Log;
import com.consumerbreak.localdbmodel.CBUser;
import com.consumerbreak.localdbmodel.Campain;
import com.consumerbreak.localdbmodel.CampainSorting;
import com.consumerbreak.localdbmodel.Category;
import com.consumerbreak.localdbmodel.Coupon;
import com.consumerbreak.localdbmodel.Filter;
import com.consumerbreak.localdbmodel.Level;
import com.consumerbreak.localdbmodel.WonCoupon;
import com.j256.ormlite.android.apptools.OrmLiteSqliteOpenHelper;
import com.j256.ormlite.dao.Dao;
import com.j256.ormlite.support.ConnectionSource;
import com.j256.ormlite.table.TableUtils;
import java.sql.SQLException;

public class CBDataBaseHelper extends OrmLiteSqliteOpenHelper {

	private static final String DATABASE_NAME = "Consumer.db";
	public static final int DATABASE_VERSION = 1;

	private final String TAG = CBDataBaseHelper.class.toString();
    private Dao<Coupon,Integer> couponDao = null;
   
	@SuppressWarnings("unused")
	private Context context;

	public CBDataBaseHelper(Context context) {
		super(context, DATABASE_NAME, null, DATABASE_VERSION);
		this.context = context;
	}

	@Override
	public void onCreate(SQLiteDatabase database,
			ConnectionSource connectionSource) {
		try {
			if (!database.isReadOnly()) {
				database.execSQL("PRAGMA foreign_keys=ON;");
			}
			createTables1(connectionSource);
		} catch (SQLException e) {
			Log.e(TAG, "onCreate", e);
		} finally {
			loadManager();
		}
	}

	@Override
	public void onUpgrade(SQLiteDatabase database,
			ConnectionSource connectionSource, int oldVersion, int newVersion) {
	}

	public void createTables(ConnectionSource connectionSource)
			throws SQLException {
		if (connectionSource == null) {
			connectionSource = getConnectionSource();
		}
		TableUtils.createTableIfNotExists(connectionSource, Coupon.class);
    }
	
	public void createTables1(ConnectionSource connectionSource)
			throws SQLException {
		if (connectionSource == null) {
			connectionSource = getConnectionSource();
		}
	    TableUtils.createTableIfNotExists(connectionSource,Coupon.class);
    }
	protected void dropAllTables( ConnectionSource connectionSource) throws SQLException {
		if (connectionSource == null) {
			connectionSource = getConnectionSource();
		}
	    TableUtils.dropTable(connectionSource, Coupon.class, true);
    }
    
	public void loadManager() {
		try {
	        couponDao = getDao(Coupon.class);
    	} catch (SQLException e) {
		}
	}

	  public Dao<Coupon, Integer> getCouponDaoManager() {
        if (couponDao == null) {
            loadManager();
        }
        return couponDao;
    }


}
