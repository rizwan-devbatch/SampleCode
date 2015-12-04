package com.android.localdbmodel;
import com.j256.ormlite.field.DatabaseField;
import com.j256.ormlite.table.DatabaseTable;
@DatabaseTable(tableName = "coupon")
public class Coupon {
    @DatabaseField(generatedId = true)
    private int id;
    @DatabaseField(columnName = "couponid")
    private String couponid;
    @DatabaseField(columnName = "userid")
    private String userid;
    @DatabaseField(columnName = "couponimgurl")
    private String couponimgurl;
    @DatabaseField(columnName = "coupondesc")
    private String coupondesc;
    @DatabaseField(columnName = "campain_id")
    private String campain_id;
    @DatabaseField(columnName = "thumbnail")
    private String thumbnail;
    @DatabaseField(columnName = "level")
    private String level;
    @DatabaseField(columnName = "saved")
    private String isSaved="no";
    @DatabaseField(columnName = "couponTitle")
    private String CouponTitle;



    public String getThumbnail() {
        return thumbnail;
    }

    public void setThumbnail(String thumbnail) {
        this.thumbnail = thumbnail;
    }

    public String getCouponid() {
        return couponid;
    }

    public void setCouponid(String couponid) {
        this.couponid = couponid;
    }

    public String getUserid() {
        return userid;
    }

    public void setUserid(String userid) {
        this.userid = userid;
    }

    public String getCouponimgurl() {
        return couponimgurl;
    }

    public void setCouponimgurl(String couponimgurl) {
        this.couponimgurl = couponimgurl;
    }

    public String getCoupondesc() {
        return coupondesc;
    }

    public void setCoupondesc(String coupondesc) {
        this.coupondesc = coupondesc;
    }

    public String getCampain_id() {
        return campain_id;
    }

    public void setCampain_id(String campain_id) {
        this.campain_id = campain_id;
    }

    public String getLevel() {
        return level;
    }

    public void setLevel(String level) {
        this.level = level;
    }
    public String getIsCouponSaved() {
        return isSaved;
    }

    public void setCouponSaved(String isSaved) {
        this.isSaved = isSaved;
    }
    public String getCouponTitle() {
        return CouponTitle;
    }
    public void setCouponTitle(String CouponTitle) {
        this.CouponTitle = CouponTitle;
    }
}
