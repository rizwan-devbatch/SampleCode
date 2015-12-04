//
//  ChatsViewController.h
//  Tellmo
//
//  Created by Waris Shams on 17/09/2015.
//  Copyright (c) 2015 Devbatch. All rights reserved.
//

#import "SelectContactForChatVC.h"
#import "TMBaseViewController.h"
#import "ChatListCell.h"


@interface ChatsViewController : TMBaseViewController <TMBaseViewControllerDelegate, UITableViewDataSource, UITableViewDelegate, UIAlertViewDelegate, NSFetchedResultsControllerDelegate>

#pragma  mark - Properties

@property (nonatomic, strong) RMPhoneFormat *phoneFormat;
@property (nonatomic, strong) NSIndexPath   *indexPathToDelete;

@property (nonatomic, strong) NSFetchedResultsController *fetchedResultsController;

#pragma  mark - Outlets
@property (weak, nonatomic) IBOutlet UILabel                *lblNoData;
@property (weak, nonatomic) IBOutlet UITableView            *tableViewChat;
@property (weak, nonatomic) IBOutlet UIImageView            *imageviewPlaceholder;

#pragma  mark - Button Action Methods

@end
