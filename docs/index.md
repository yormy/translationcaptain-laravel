# Documentation

## Prinicples
* The most recent referrer will be credited for the action. So if Jim refers Bob, Later Alice refers bob and bob takes
an action then Alice is to be awarded for the action
* Rewards will be awarded for the lifetime of the (cookie) or user (after signup) , so even if bob does not upgrade for a year after signup
when he finally does the referrer that referred bob the most recent time will be credited for the action

# Users are assigned a referrer through the query parameters


## Award actions of user to a referrer
```
use Yormy\ReferralSystem\Observers\Events\AwardReferrerEvent;
use Yormy\ReferralSystem\Models\ReferralAction;

event(new AwardReferrerEvent(ReferralAction::UPGRADE_SILVER));
```

## Revoke awarded actions
For example if a user signs up and then cancels there is a need to revoke the awards given out to the referrer.
Just call the revoke event with the same actionId. This will softdelete the item and add the desciption to the item
```
event(new AwardRevokeEvent(ReferralAction::UPGRADE_SILVER, "description"));
```

## Changes
* [TODO ITEMS](todo.md)
* [CHANGELOG](../CHANGELOG.md)

