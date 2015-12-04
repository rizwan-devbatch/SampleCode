//
//  ChatsViewController.m
//  Tellmo
//
//  Created by Waris Shams on 17/09/2015.
//  Copyright (c) 2015 Devbatch. All rights reserved.
//

#import "ChatDetailViewController.h"
#import "ChatsViewController.h"

@interface ChatsViewController ()

@end

@implementation ChatsViewController

#pragma mark - View Life Cycle Starts here...
- (void)viewDidLoad {
    [super viewDidLoad];
    
    [self setview];
}

-(void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(messageReceived:) name:kChatMessageReceived object:nil];
    self.baseDelegate = self;
    
    [CommonUtility sharedUtility].isChatDetailViewOpen = NO;
    
    self.imageviewPlaceholder.hidden    = !self.lblNoData.hidden;
}

- (void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    
    [self fetchChatThreads];
}


- (void)viewWillDisappear:(BOOL)animated{
    [super viewWillDisappear:animated];
    
    self.baseDelegate = nil;
    [[NSNotificationCenter defaultCenter] removeObserver:self name:kChatMessageReceived object:nil];
}

#pragma mark - Initialization Methods

-(void)setview {
    [self setupNavigationBarTitle:@"Chats" showRightButton:YES leftButtonType:UINavigationBarLeftButtonTypeNone rightButtonType:UINavigationBarRightButtonTypeAdd setTitleViewWithType:TitleViewOfTypeNone];
    
    self.tableViewChat.tableFooterView  = [[UIView alloc] initWithFrame:CGRectZero];
    NSError *error = nil;
    if ([[self fetchedResultsController] performFetch:&error]) {
        [self updateView];
    }
    else{
        [self updateView];
    }
}

#pragma mark - Segues

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    if ([segue.identifier isEqualToString:@"Chat Detail Push"]) {
        ChatDetailViewController *vc = segue.destinationViewController;
        if ([sender isKindOfClass:[ChatListCell class]]) {
            ChatListCell *cell = (ChatListCell *)sender;
            vc.currentThread = [self.fetchedResultsController objectAtIndexPath:[self.tableViewChat indexPathForCell:cell]];
            vc.title = cell.lblContactName.text;
        }
        else{
            vc.threadID = [sender objectForKey:@"threadId"];
            vc.title = [sender objectForKey:@"title"];
        }
    }
}

#pragma mark - Custom Methods

-(void)fetchChatThreads {
    NSError *error = nil;
    if ([[self fetchedResultsController] performFetch:&error]) {
        [self updateView];
    }
    else{
        [self updateView];
    }
}

-(void)updateView {
    if (self.fetchedResultsController.fetchedObjects.count > 0) {
        self.lblNoData.hidden       = YES;
        self.tableViewChat.hidden   = NO;
        [self.tableViewChat reloadData];
    }else{
        self.lblNoData.hidden       = NO;
        self.tableViewChat.hidden   = YES;
    }
}

-(void)messageReceived:(NSNotification *)notif {
    [self fetchChatThreads];
}

-(void)deleteChat{
    [AppUtility showProgressHudWithStatus:@"Deleting..."];
    
    Thread *thread = [self.fetchedResultsController objectAtIndexPath:self.indexPathToDelete];
    
    ChatManager *chatManager = [[ChatManager alloc] init];
    
    [chatManager deleteChatWithThread:thread AndCompletionHandler:^(BOOL isSuccess) {
        [SVProgressHUD dismiss];
        if (isSuccess) {
            [self updateView];
        }
    }];
}

#pragma mark - Fetched results controller

- (NSFetchedResultsController *)fetchedResultsController {
    
    if (_fetchedResultsController != nil) {
        return _fetchedResultsController;
    }
    
    NSFetchRequest *fetchRequest                = [[NSFetchRequest alloc] init];
    NSEntityDescription *entity                 = [NSEntityDescription entityForName:@"Thread" inManagedObjectContext:[CoreDataHelper managedObjectContext]];
    [fetchRequest setEntity:entity];
    NSSortDescriptor *sorting   = [NSSortDescriptor sortDescriptorWithKey:@"sentAt" ascending:NO];
    [fetchRequest setSortDescriptors:@[sorting]];
    
    NSPredicate *predicate = [NSPredicate predicateWithFormat:@"lastMessageSentBy != nil OR threadOpponent.length > 0"];
    [fetchRequest setPredicate:predicate];
    [fetchRequest setFetchBatchSize:100];
    
    [NSFetchedResultsController deleteCacheWithName:kChatCacheName];
    NSFetchedResultsController *theFetchedResultsController = [[NSFetchedResultsController alloc] initWithFetchRequest:fetchRequest managedObjectContext:[CoreDataHelper managedObjectContext] sectionNameKeyPath:nil cacheName:kChatCacheName];
    
    _fetchedResultsController               = theFetchedResultsController;
    _fetchedResultsController.delegate          = self;
    
    return _fetchedResultsController;
}

- (void)controllerWillChangeContent:(NSFetchedResultsController *)controller {
    [self.tableViewChat beginUpdates];
}

- (void)controller:(NSFetchedResultsController *)controller didChangeObject:(id)anObject atIndexPath:(NSIndexPath *)indexPath forChangeType:(NSFetchedResultsChangeType)type newIndexPath:(NSIndexPath *)newIndexPath {
    switch(type){
        case NSFetchedResultsChangeInsert:
            [self.tableViewChat insertRowsAtIndexPaths:@[newIndexPath] withRowAnimation:UITableViewRowAnimationFade];
            break;
            
        case NSFetchedResultsChangeDelete:
            [self.tableViewChat deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] withRowAnimation:UITableViewRowAnimationFade];
            break;
            
        case NSFetchedResultsChangeUpdate:
            [self configureCell:(ChatListCell *)[self.tableViewChat cellForRowAtIndexPath:indexPath] forIndexPath:indexPath];
            break;
            
        case NSFetchedResultsChangeMove:
            [self.tableViewChat deleteRowsAtIndexPaths:[NSArray
                                               arrayWithObject:indexPath] withRowAnimation:UITableViewRowAnimationFade];
            [self.tableViewChat insertRowsAtIndexPaths:[NSArray
                                               arrayWithObject:newIndexPath] withRowAnimation:UITableViewRowAnimationFade];
            break;
    }
}

- (void)controller:(NSFetchedResultsController *)controller didChangeSection:(id )sectionInfo atIndex:(NSUInteger)sectionIndex forChangeType:(NSFetchedResultsChangeType)type {
    switch(type){
        case NSFetchedResultsChangeInsert:
            [self.tableViewChat insertSections:[NSIndexSet indexSetWithIndex:sectionIndex] withRowAnimation:UITableViewRowAnimationFade];
            break;
            
        case NSFetchedResultsChangeDelete:
            [self.tableViewChat deleteSections:[NSIndexSet indexSetWithIndex:sectionIndex] withRowAnimation:UITableViewRowAnimationFade];
            break;
            
        default:
            break;
    }
}

- (void)controllerDidChangeContent:(NSFetchedResultsController *)controller {
    [self.tableViewChat endUpdates];
}


#pragma mark - Animation Methods

#pragma mark - Gesture Handler Methods

#pragma mark - Button Action Methods


#pragma mark - DELEGATE METHODS

#pragma mark- BaseViewControllerDelegate

- (void)rightNavigationBarButtonClicked{
    SelectContactForChatVC *contactsVC  = [self.storyboard instantiateViewControllerWithIdentifier:@"SelectContactForChatVC"];
    [self.navigationController pushViewController:contactsVC animated:YES];
}

#pragma mark Tableview

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath{
    return 72.0;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [self.fetchedResultsController.fetchedObjects count];
}

- (void)configureCell:(ChatListCell *)cell forIndexPath:(NSIndexPath *)indexPath{
    Thread *record                  = [self.fetchedResultsController objectAtIndexPath:indexPath];
    Contact *myContact              = record.lastMessageSentBy;
    NSString *contactName           = [NSString stringWithFormat:@"%@", record.threadId];
    
    if (myContact.recordId) {
        NSString *contactRecordId       = [NSString stringWithFormat:@"%ld",(long)[myContact.recordId integerValue]];
        if (myContact.firstName.length > 0) {
            contactName                 = myContact.firstName;
            if (myContact.lastName.length > 0) {
                contactName             = [NSString stringWithFormat:@"%@ %@", contactName, myContact.lastName];
            }
        }else if (myContact.lastName.length > 0) {
            contactName                 = myContact.lastName;
        }
        
        UIImage *contactImage           = [[SDImageCache_Tellmo sharedImageCache] imageFromDiskCacheForKey:contactRecordId];
        
        if (contactImage) {
            cell.contactImage.image     = contactImage;
        }
        else
            cell.contactImage.image = [UIImage imageNamed:@"icon-avatar"];
    }
    
    cell.lblContactName.text        = [contactName capitalizedString];
    cell.lblMessage.text            = record.lastMessage;
    cell.lblTimeAgo.text            = [record.sentAt timeAgo];
}

-(UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    static NSString *identifier     = @"cell";
    
    ChatListCell *cell              = (ChatListCell *)[tableView dequeueReusableCellWithIdentifier:identifier forIndexPath:indexPath];
    [self configureCell:cell forIndexPath:indexPath];
    
    return cell;
}

- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath {
    return YES;
}

- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath {
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        self.indexPathToDelete = indexPath;
        UIAlertView *av = [[UIAlertView alloc] initWithTitle:@"Confirm!" message:@"Are you sure to delete?" delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
        [av show];
    }
}

#pragma mark Textfield


#pragma mark Alertview

-(void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    switch (buttonIndex) {
        case 1:
            [self deleteChat];
            break;
            
        default:
            break;
    }
}

#pragma mark - View Life Cycle Ends here...
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
}

@end


