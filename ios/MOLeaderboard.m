//
//  MOLeaderboardList.m
//
//  Created by Zeeshan on 17/09/2015
//  Copyright (c) 2015 DevBatch. All rights reserved.
//

#import "MOLeaderboard.h"


NSString *const kMOLeaderboardListId = @"id";
NSString *const kMOLeaderboardListPlaytime = @"playtime";
NSString *const kMOLeaderboardListPpLength = @"pp_length";
NSString *const kMOLeaderboardListCampaignRevisionId = @"campaignRevision_id";
NSString *const kMOLeaderboardListName = @"name";
NSString *const kMOLeaderboardListDatetime = @"datetime";
NSString *const kMOLeaderboardListActualplay = @"actualplay";
NSString *const kMOLeaderboardListWinnerTime = @"winner_time";
NSString *const kMOLeaderboardListTotalplays = @"totalplays";
NSString *const kMOLeaderboardListUrlThumbImage = @"thumbimage";
NSString *const kMOLeaderboardListAdvertiserId = @"advertiser_id";
NSString *const kMOLeaderboardListReward = @"reward";
NSString *const kMOLeaderboardListAdsmashcampid = @"adsmashcampid";
NSString *const kMOLeaderboardListPlayperiodend = @"playperiodend";
NSString *const kMOLeaderboardListCampaigntype = @"campaigntype";
NSString *const kMOLeaderboardListPlayperiodstart = @"playperiodstart";
NSString *const kMOLeaderboardListConsumerid = @"consumerid";
NSString *const kMOLeaderboardListYourTime = @"your_time";
NSString *const kMOLeaderboardListRewarded = @"rewarded";
NSString *const kMOLeaderboardListAdvertiserName = @"advertiser_name";
NSString *const kMOLeaderboardListPlayed = @"played";

@interface MOLeaderboard ()

- (id)objectOrNilForKey:(id)aKey fromDictionary:(NSDictionary *)dict;

@end

@implementation MOLeaderboard

@synthesize leaderboardListIdentifier = _leaderboardListIdentifier;
@synthesize playtime = _playtime;
@synthesize ppLength = _ppLength;
@synthesize campaignRevisionId = _campaignRevisionId;
@synthesize name = _name;
@synthesize datetime = _datetime;
@synthesize actualplay = _actualplay;
@synthesize winnerTime = _winnerTime;
@synthesize totalplays = _totalplays;
@synthesize urlThumbImage = _urlThumbImage;
@synthesize reward = _reward;
@synthesize adsmashcampid = _adsmashcampid;
@synthesize playperiodend = _playperiodend;
@synthesize campaigntype = _campaigntype;
@synthesize playperiodstart = _playperiodstart;
@synthesize consumerid = _consumerid;
@synthesize yourTime = _yourTime;
@synthesize rewarded = _rewarded;
@synthesize advertiserName=_advertiserName;
@synthesize advertiserid = _advertiserid;
@synthesize played=_played;

+ (MOLeaderboard *)modelObjectWithDictionary:(NSDictionary *)dict
{
    MOLeaderboard *instance = [[MOLeaderboard alloc] initWithDictionary:dict];
    return instance;
}

- (instancetype)initWithDictionary:(NSDictionary *)dict
{
    self = [super init];
    
    // This check serves to make sure that a non-NSDictionary object
    // passed into the model class doesn't break the parsing.
    if(self && [dict isKindOfClass:[NSDictionary class]]) {
            self.leaderboardListIdentifier = [self objectOrNilForKey:kMOLeaderboardListId fromDictionary:dict];
            self.playtime = [self objectOrNilForKey:kMOLeaderboardListPlaytime fromDictionary:dict];
            self.ppLength = [self objectOrNilForKey:kMOLeaderboardListPpLength fromDictionary:dict];
            self.campaignRevisionId = [self objectOrNilForKey:kMOLeaderboardListCampaignRevisionId fromDictionary:dict];
            self.name = [self objectOrNilForKey:kMOLeaderboardListName fromDictionary:dict];
            self.datetime = [self objectOrNilForKey:kMOLeaderboardListDatetime fromDictionary:dict];
            self.actualplay = [self objectOrNilForKey:kMOLeaderboardListActualplay fromDictionary:dict];
            self.winnerTime = [self objectOrNilForKey:kMOLeaderboardListWinnerTime fromDictionary:dict];
            self.totalplays = @([[self objectOrNilForKey:kMOLeaderboardListTotalplays fromDictionary:dict] integerValue]);
            self.urlThumbImage = [self objectOrNilForKey:kMOLeaderboardListUrlThumbImage fromDictionary:dict];
            self.reward = [self objectOrNilForKey:kMOLeaderboardListReward fromDictionary:dict];
            self.adsmashcampid = [self objectOrNilForKey:kMOLeaderboardListAdsmashcampid fromDictionary:dict];
            self.playperiodend = [self objectOrNilForKey:kMOLeaderboardListPlayperiodend fromDictionary:dict];
            self.campaigntype = [self objectOrNilForKey:kMOLeaderboardListCampaigntype fromDictionary:dict];
            self.playperiodstart = [self objectOrNilForKey:kMOLeaderboardListPlayperiodstart fromDictionary:dict];
            self.consumerid = [self objectOrNilForKey:kMOLeaderboardListConsumerid fromDictionary:dict];
            self.yourTime = [self objectOrNilForKey:kMOLeaderboardListYourTime fromDictionary:dict];
            self.rewarded = [self objectOrNilForKey:kMOLeaderboardListRewarded fromDictionary:dict];
            self.advertiserName = [self objectOrNilForKey:kMOLeaderboardListAdvertiserName fromDictionary:dict];
            self.advertiserid = [self objectOrNilForKey:kMOLeaderboardListAdvertiserId fromDictionary:dict];
            self.played = [self objectOrNilForKey:kMOLeaderboardListPlayed fromDictionary:dict];
    }
    
    return self;
    
}

- (NSDictionary *)dictionaryRepresentation
{
    NSMutableDictionary *mutableDict = [NSMutableDictionary dictionary];
    [mutableDict setValue:self.leaderboardListIdentifier forKey:kMOLeaderboardListId];
    [mutableDict setValue:self.playtime forKey:kMOLeaderboardListPlaytime];
    [mutableDict setValue:self.ppLength forKey:kMOLeaderboardListPpLength];
    [mutableDict setValue:self.campaignRevisionId forKey:kMOLeaderboardListCampaignRevisionId];
    [mutableDict setValue:self.name forKey:kMOLeaderboardListName];
    [mutableDict setValue:self.datetime forKey:kMOLeaderboardListDatetime];
    [mutableDict setValue:self.actualplay forKey:kMOLeaderboardListActualplay];
    [mutableDict setValue:self.winnerTime forKey:kMOLeaderboardListWinnerTime];
    [mutableDict setValue:self.totalplays forKey:kMOLeaderboardListTotalplays];
    [mutableDict setValue:self.urlThumbImage forKey:kMOLeaderboardListUrlThumbImage];
    [mutableDict setValue:self.reward forKey:kMOLeaderboardListReward];
    [mutableDict setValue:self.adsmashcampid forKey:kMOLeaderboardListAdsmashcampid];
    [mutableDict setValue:self.playperiodend forKey:kMOLeaderboardListPlayperiodend];
    [mutableDict setValue:self.campaigntype forKey:kMOLeaderboardListCampaigntype];
    [mutableDict setValue:self.playperiodstart forKey:kMOLeaderboardListPlayperiodstart];
    [mutableDict setValue:self.consumerid forKey:kMOLeaderboardListConsumerid];
    [mutableDict setValue:self.yourTime forKey:kMOLeaderboardListYourTime];
    [mutableDict setValue:self.rewarded forKey:kMOLeaderboardListRewarded];
    [mutableDict setValue:self.advertiserName forKey:kMOLeaderboardListAdvertiserName];
    [mutableDict setValue:self.advertiserid forKey:kMOLeaderboardListAdvertiserId];
    [mutableDict setValue:self.played forKey:kMOLeaderboardListPlayed];
    return [NSDictionary dictionaryWithDictionary:mutableDict];
}

- (NSString *)description 
{
    return [NSString stringWithFormat:@"%@", [self dictionaryRepresentation]];
}

#pragma mark - Helper Method
- (id)objectOrNilForKey:(id)aKey fromDictionary:(NSDictionary *)dict
{
    id object = [dict objectForKey:aKey];
    return [object isEqual:[NSNull null]] ? nil : object;
}


#pragma mark - NSCoding Methods

- (id)initWithCoder:(NSCoder *)aDecoder
{
    self = [super init];

    self.leaderboardListIdentifier = [aDecoder decodeObjectForKey:kMOLeaderboardListId];
    self.playtime = [aDecoder decodeObjectForKey:kMOLeaderboardListPlaytime];
    self.ppLength = [aDecoder decodeObjectForKey:kMOLeaderboardListPpLength];
    self.campaignRevisionId = [aDecoder decodeObjectForKey:kMOLeaderboardListCampaignRevisionId];
    self.name = [aDecoder decodeObjectForKey:kMOLeaderboardListName];
    self.datetime = [aDecoder decodeObjectForKey:kMOLeaderboardListDatetime];
    self.actualplay = [aDecoder decodeObjectForKey:kMOLeaderboardListActualplay];
    self.winnerTime = [aDecoder decodeObjectForKey:kMOLeaderboardListWinnerTime];
    self.totalplays = [aDecoder decodeObjectForKey:kMOLeaderboardListTotalplays];
    self.urlThumbImage = [aDecoder decodeObjectForKey:kMOLeaderboardListUrlThumbImage];
    self.reward = [aDecoder decodeObjectForKey:kMOLeaderboardListReward];
    self.adsmashcampid = [aDecoder decodeObjectForKey:kMOLeaderboardListAdsmashcampid];
    self.playperiodend = [aDecoder decodeObjectForKey:kMOLeaderboardListPlayperiodend];
    self.campaigntype = [aDecoder decodeObjectForKey:kMOLeaderboardListCampaigntype];
    self.playperiodstart = [aDecoder decodeObjectForKey:kMOLeaderboardListPlayperiodstart];
    self.consumerid = [aDecoder decodeObjectForKey:kMOLeaderboardListConsumerid];
    self.yourTime = [aDecoder decodeObjectForKey:kMOLeaderboardListYourTime];
    self.rewarded = [aDecoder decodeObjectForKey:kMOLeaderboardListRewarded];
    self.advertiserName = [aDecoder decodeObjectForKey:kMOLeaderboardListAdvertiserName];
    self.advertiserid = [aDecoder decodeObjectForKey:kMOLeaderboardListAdvertiserId];
    self.played = [aDecoder decodeObjectForKey:kMOLeaderboardListPlayed];
    return self;
}

- (void)encodeWithCoder:(NSCoder *)aCoder
{

    [aCoder encodeObject:_leaderboardListIdentifier forKey:kMOLeaderboardListId];
    [aCoder encodeObject:_playtime forKey:kMOLeaderboardListPlaytime];
    [aCoder encodeObject:_ppLength forKey:kMOLeaderboardListPpLength];
    [aCoder encodeObject:_campaignRevisionId forKey:kMOLeaderboardListCampaignRevisionId];
    [aCoder encodeObject:_name forKey:kMOLeaderboardListName];
    [aCoder encodeObject:_datetime forKey:kMOLeaderboardListDatetime];
    [aCoder encodeObject:_actualplay forKey:kMOLeaderboardListActualplay];
    [aCoder encodeObject:_winnerTime forKey:kMOLeaderboardListWinnerTime];
    [aCoder encodeObject:_totalplays forKey:kMOLeaderboardListTotalplays];
    [aCoder encodeObject:_urlThumbImage forKey:kMOLeaderboardListUrlThumbImage];
    [aCoder encodeObject:_reward forKey:kMOLeaderboardListReward];
    [aCoder encodeObject:_adsmashcampid forKey:kMOLeaderboardListAdsmashcampid];
    [aCoder encodeObject:_playperiodend forKey:kMOLeaderboardListPlayperiodend];
    [aCoder encodeObject:_campaigntype forKey:kMOLeaderboardListCampaigntype];
    [aCoder encodeObject:_playperiodstart forKey:kMOLeaderboardListPlayperiodstart];
    [aCoder encodeObject:_consumerid forKey:kMOLeaderboardListConsumerid];
    [aCoder encodeObject:_yourTime forKey:kMOLeaderboardListYourTime];
    [aCoder encodeObject:_rewarded forKey:kMOLeaderboardListRewarded];
    [aCoder encodeObject:_advertiserName forKey:kMOLeaderboardListAdvertiserName];
    [aCoder encodeObject:_advertiserid forKey:kMOLeaderboardListAdvertiserId];
    [aCoder encodeObject:_played forKey:kMOLeaderboardListPlayed];
}

- (id)copyWithZone:(NSZone *)zone
{
    MOLeaderboard *copy = [[MOLeaderboard alloc] init];
    
    if (copy) {

        copy.leaderboardListIdentifier = [self.leaderboardListIdentifier copyWithZone:zone];
        copy.playtime = [self.playtime copyWithZone:zone];
        copy.ppLength = [self.ppLength copyWithZone:zone];
        copy.campaignRevisionId = [self.campaignRevisionId copyWithZone:zone];
        copy.name = [self.name copyWithZone:zone];
        copy.datetime = [self.datetime copyWithZone:zone];
        copy.actualplay = [self.actualplay copyWithZone:zone];
        copy.winnerTime = [self.winnerTime copyWithZone:zone];
        copy.totalplays = [self.totalplays copyWithZone:zone];
        copy.urlThumbImage = [self.urlThumbImage copyWithZone:zone];
        copy.reward = [self.reward copyWithZone:zone];
        copy.adsmashcampid = [self.adsmashcampid copyWithZone:zone];
        copy.playperiodend = [self.playperiodend copyWithZone:zone];
        copy.campaigntype = [self.campaigntype copyWithZone:zone];
        copy.playperiodstart = [self.playperiodstart copyWithZone:zone];
        copy.consumerid = [self.consumerid copyWithZone:zone];
        copy.yourTime = [self.yourTime copyWithZone:zone];
        copy.rewarded = [self.rewarded copyWithZone:zone];
        copy.advertiserName = [self.advertiserName copyWithZone:zone];
        copy.advertiserid = [self.advertiserid copyWithZone:zone];
        copy.played = [self.played copyWithZone:zone];
    }
    
    return copy;
}


@end
