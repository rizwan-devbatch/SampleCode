//
//  MOLeaderboardList.h
//
//  Created by Zeeshan on 17/09/2015
//  Copyright (c) 2015 DevBatch. All rights reserved.//

#import <Foundation/Foundation.h>



@interface MOLeaderboard : NSObject <NSCoding, NSCopying>

@property (nonatomic, strong) NSString *leaderboardListIdentifier;
@property (nonatomic, strong) NSString *playtime;
@property (nonatomic, strong) NSString *ppLength;
@property (nonatomic, strong) NSString *campaignRevisionId;
@property (nonatomic, strong) NSString *name;
@property (nonatomic, strong) NSString *datetime;
@property (nonatomic, strong) NSString *actualplay;
@property (nonatomic, strong) NSString *winnerTime;
@property (nonatomic, strong) NSNumber *totalplays;
@property (nonatomic, strong) NSString *urlThumbImage;
@property (nonatomic, strong) NSString *reward;
@property (nonatomic, strong) id adsmashcampid;
@property (nonatomic, strong) NSString *playperiodend;
@property (nonatomic, strong) NSString *campaigntype;
@property (nonatomic, strong) NSString *playperiodstart;
@property (nonatomic, strong) NSString *consumerid;
@property (nonatomic, strong) NSString *advertiserid;
@property (nonatomic, strong) NSString *yourTime;
@property (nonatomic, strong) NSString *rewarded;
@property (nonatomic, strong) NSString *advertiserName;
@property (nonatomic, strong) NSString *played;

+ (MOLeaderboard *)modelObjectWithDictionary:(NSDictionary *)dict;
- (instancetype)initWithDictionary:(NSDictionary *)dict;
- (NSDictionary *)dictionaryRepresentation;

@end
