package com.android.models;

public class Coupon {
    private int id;
    private String couponHeading;
    private String couponDescription;
    private String couponImageUrl;
    private String consumerId;
    private String campainId;
    private String consumed;
    private String couponId;
    private String couponLevel;
    private Campain campain;

    public Coupon(String couponImageUrl,String couponHeading, String couponDescription) {
        this.couponHeading = couponHeading;
        this.couponDescription = couponDescription;
        this.couponImageUrl = couponImageUrl;
    }

    public Coupon(String consumerId, String campainId, String consumed, String couponId, String couponLevel, String couponImageUrl,Campain campain,String couponHeading,String couponDescription) {
        this.consumerId = consumerId;
        this.campainId = campainId;
        this.consumed = consumed;
        this.couponId = couponId;
        this.couponLevel = couponLevel;
        this.couponImageUrl = couponImageUrl;
        this.campain = campain;
        this.couponHeading = couponHeading;
        this.couponDescription = couponDescription;
    }

    public String getConsumerId() {
        return consumerId;
    }

    public void setConsumerId(String consumerId) {
        this.consumerId = consumerId;
    }

    public String getCampainId() {
        return campainId;
    }

    public void setCampainId(String campainId) {
        this.campainId = campainId;
    }

    public String getConsumed() {
        return consumed;
    }

    public void setConsumed(String consumed) {
        this.consumed = consumed;
    }

    public String getCouponId() {
        return couponId;
    }

    public void setCouponId(String couponId) {
        this.couponId = couponId;
    }

    public String getCouponLevel() {
        return couponLevel;
    }

    public void setCouponLevel(String couponLevel) {
        this.couponLevel = couponLevel;
    }



    public String getCouponImageUrl() {
        return couponImageUrl;
    }

    public void setCouponImageUrl(String couponImageUrl) {
        this.couponImageUrl = couponImageUrl;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getCouponHeading() {
        return couponHeading;
    }

    public void setCouponHeading(String couponHeading) {
        this.couponHeading = couponHeading;
    }

    public String getCouponDescription() {
        return couponDescription;
    }

    public void setCouponDescription(String couponDescription) {
        this.couponDescription = couponDescription;
    }
    public Campain getCampaign(){
        return campain;
    }
}
