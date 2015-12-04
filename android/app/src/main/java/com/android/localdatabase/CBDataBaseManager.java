package com.android.localdatabase;

import android.content.Context;
import android.os.AsyncTask;
import android.os.Environment;
import android.os.PowerManager;
import android.util.Log;
import com.j256.ormlite.android.apptools.OpenHelperManager;
import com.j256.ormlite.dao.Dao;
import com.j256.ormlite.stmt.PreparedQuery;
import com.j256.ormlite.stmt.QueryBuilder;
import com.j256.ormlite.stmt.UpdateBuilder;
import com.j256.ormlite.stmt.Where;
import com.j256.ormlite.support.ConnectionSource;
import org.json.JSONArray;
import org.json.JSONObject;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.sql.SQLException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.List;


public class CBDataBaseManager {
    @SuppressWarnings("unused")
    private ImageDownloader imageDownloader;
    private Context context;
    public static CBDataBaseHelper helper;
    int counter = 0;
    private final String TAG = CBDataBaseManager.class.getName()
            .toString();

    public CBDataBaseManager(Context context, boolean recreate) {
        this.context = context;
        if (recreate) {
            this.helper = (CBDataBaseHelper) OpenHelperManager.getHelper(
                    context.getApplicationContext(),
                    CBDataBaseHelper.class);
            helper.getWritableDatabase();
        } else {
            if (this.helper == null) {
                this.helper = (CBDataBaseHelper) OpenHelperManager
                        .getHelper(context.getApplicationContext(),
                                CBDataBaseHelper.class);
                // Log.e(TAG, "Created helper!");
            } else {
                Log.e(TAG, "No HELPER");
            }
        }
    }

    public void dropAllTables(ConnectionSource connectionSource)
            throws SQLException {

        helper.dropAllTables(helper.getConnectionSource());
    }

    public void reCreateAllTables() throws SQLException {
        helper.createTables(helper.getConnectionSource());
    }

    /**
     * @return the helper
     */
    protected CBDataBaseHelper getHelper() {
        return helper;
    }

    /**
     * @param helper
     *            the helper to set
     */
    protected void setHelper(CBDataBaseHelper helper) {
        this.helper = helper;
    }
    
    public List<Coupon> getAllCoupons(String userId){
        List<Coupon> listCoupon = null;
        try{
            QueryBuilder<Coupon, Integer> queryBuilder = helper.getCouponDaoManager().queryBuilder();
            Where where = queryBuilder.where();
            where.eq("userid",userId);
            PreparedQuery<Coupon> preparedQueryMenu = queryBuilder.prepare();
            listCoupon = helper.getCouponDaoManager().query(preparedQueryMenu);
            if(listCoupon.size() > 0){
                return listCoupon;
            }else{
                return  null;
            }
        }catch (Exception e){
            return  null;
        }
    }
    
    public Coupon getCouponAtCurrentOrNextLevel(String camp_id,String currentLevel,String userId){
        List<Coupon> list  = null;
        try {
            QueryBuilder<Coupon, Integer> queryBuilder = helper.getCouponDaoManager().queryBuilder();
            Where where = queryBuilder.where();
            where.and(where.eq("campain_id", camp_id), where.eq("userid", userId), where.eq("saved", "no"), where.ge("level", currentLevel));
            PreparedQuery<Coupon> preparedQueryMenu = queryBuilder.prepare();
            list = helper.getCouponDaoManager().query(preparedQueryMenu);
            if(list.size()>0){
                return list.get(0);
            }else{
                return null;
            }
        }catch (Exception e) {
            return null;
            // TODO: handle exception
        }
    }
    
}
