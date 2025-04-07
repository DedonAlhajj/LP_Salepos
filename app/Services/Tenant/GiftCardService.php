<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\DTOs\GiftCardDTO;
use App\DTOs\GiftCardUpdateDTO;
use App\DTOs\RechargeGiftCardDTO;
use App\Mail\General;
use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\GiftCardRecharge;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class GiftCardService
{

    protected SendMailAction $sendMailAction;

    public function __construct(SendMailAction $sendMailAction)
    {
        $this->sendMailAction = $sendMailAction;
    }

    /**
     * Retrieve essential data for the index view.
     *
     * This method fetches all customers, active users, and gift cards,
     * ordering gift cards by descending ID.
     *
     * @return array An array containing customer list, active user list, and gift card list.
     * @throws \Exception If fetching gift cards fails.
     */
    #[ArrayShape(['lims_customer_list' => "mixed", 'lims_user_list' => "mixed", 'lims_gift_card_all' => "mixed"])]
    public function getIndexData(): array
    {
        try {
            return [
                // Retrieve all customers from the database
                'lims_customer_list' => Customer::all(),

                // Fetch all active users
                'lims_user_list' => User::where('is_active', true)->get(),

                // Get all gift cards ordered by ID in descending order
                'lims_gift_card_all' => GiftCard::orderBy('id', 'desc')->get(),
            ];
        } catch (\Exception $e) {
            // Log the error with a descriptive message
            Log::error("Failed to fetch gift card: " . $e->getMessage());

            // Throw a generic exception to the caller
            throw new \Exception('Failed to fetch gift card, please try again');
        }
    }

    /**
     * Generate a unique numeric code for a gift card.
     *
     * This method utilizes UUID, removes non-numeric characters,
     * and ensures a 10-digit unique code.
     *
     * @return string A unique 10-digit numeric code.
     * @throws \Exception If code generation fails.
     */
    public function generateCode(): string
    {
        try {
            // Generate a UUID and remove dashes
            $uniqueNumber = str_replace('-', '', Str::uuid());

            // Keep only numeric characters from the UUID
            $uniqueNumber = preg_replace('/[^0-9]/', '', $uniqueNumber);

            // Return the first 10 digits to ensure a short unique code
            return substr($uniqueNumber, 0, 10);

        } catch (\Exception $e) {
            // Handle any unexpected errors during code generation
            throw new \Exception('Failed to generate Code gift card, please try again');
        }
    }

    /**
     * Create a new gift card.
     *
     * This method uses a database transaction to ensure atomicity when creating
     * a gift card and sending a notification email.
     *
     * @param GiftCardDTO $dto Data transfer object containing gift card details.
     * @return mixed The response from the email sending action.
     * @throws \Exception If gift card creation fails.
     */
    public function createGiftCard(GiftCardDTO $dto): mixed
    {
        try {
            return DB::transaction(function () use ($dto) {
                // Persist the gift card data in the database
                GiftCard::create($dto->toArray());

                // Send an email notification upon gift card creation
                $message = $this->sendMailAction->sendMail(
                    $dto->toArray(),
                    General::class,
                    "Tenant.mail.gift_card_create"
                );

                return $message;
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Failed to create gift card: " . $e->getMessage());

            // Throw an exception to the caller
            throw new \Exception('Failed to create gift card, please try again');
        }
    }

    /**
     * Retrieve a gift card by its ID.
     *
     * This method fetches the gift card details or throws an exception if not found.
     *
     * @param int $id The ID of the gift card.
     * @return GiftCard The gift card instance.
     * @throws \Exception If the gift card is not found.
     */
    public function edit(int $id): GiftCard
    {
        try {
            // Find the gift card or fail if not found
            return GiftCard::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Log the error indicating the card was not found
            Log::error('GiftCard not found (GiftCard): ' . $e->getMessage());

            // Throw an exception to inform the caller
            throw new \Exception('GiftCard not found');
        }
    }

    /**
     * Update an existing gift card.
     *
     * This method ensures data integrity using a transaction and updates gift card details.
     *
     * @param GiftCardUpdateDTO $dto Data transfer object containing updated gift card details.
     * @return bool True if the update was successful.
     * @throws \Exception If the gift card is not found or update fails.
     */
    public function updateGiftCard(GiftCardUpdateDTO $dto): bool
    {
        try {
            return DB::transaction(function () use ($dto) {
                // Retrieve the gift card by ID or fail if not found
                $giftCard = GiftCard::findOrFail($dto->id);

                // Update gift card details
                $giftCard->update([
                    'card_no' => $dto->cardNo,
                    'amount' => $dto->amount,
                    'user_id' => $dto->userId,
                    'customer_id' => $dto->customerId,
                    'expired_date' => $dto->expiredDate
                ]);

                return true;
            });
        } catch (ModelNotFoundException $e) {
            // Log error indicating the gift card does not exist
            Log::error('GiftCard not found: ' . $e->getMessage());

            // Throw an exception with detailed information
            throw new \Exception("GiftCard not found with ID: {$dto->id}");
        } catch (\Exception $e) {
            // Log any unexpected error during the update process
            Log::error('Error Updating GiftCard: ' . $e->getMessage());

            // Throw a generic exception for error handling
            throw new \Exception("Something went wrong while updating the GiftCard");
        }
    }

    /**
     * Delete multiple gift cards from the database.
     *
     * This method removes gift cards using their IDs. If a gift card is not found,
     * an exception is thrown.
     *
     * @param array $GiftCardIds Array of gift card IDs to be deleted.
     * @return bool True if deletion is successful.
     * @throws \Exception If the gift card is not found or deletion fails.
     */
    public function deleteGiftCard(array $GiftCardIds): bool
    {
        try {
            // Delete gift cards matching the provided IDs
            GiftCard::whereIn('id', $GiftCardIds)->delete();

            return true;
        } catch (ModelNotFoundException $e) {
            // Log error when a gift card is not found
            Log::error('Gift Card not found: ' . $e->getMessage());

            // Throw exception for error handling
            throw new Exception('Gift Card not found: ' . $e->getMessage());
        } catch (Exception $e) {
            // Log general deletion error
            Log::error('Error deleting GiftCard: ' . $e->getMessage());

            // Throw exception for general failure
            throw new Exception('An error occurred while deleting the Gift Card.');
        }
    }

    /**
     * Delete a single gift card by its ID.
     *
     * This method retrieves the gift card and deletes it. If the card is not found,
     * an exception is thrown.
     *
     * @param int $id The ID of the gift card to be deleted.
     * @throws \Exception If the gift card is not found or deletion fails.
     */
    public function destroy(int $id)
    {
        try {
            // Retrieve and delete the gift card
            GiftCard::findOrFail($id)->delete();

        } catch (ModelNotFoundException $e) {
            // Log error when the gift card is not found
            Log::error('GiftCard not found: ' . $e->getMessage());

            // Throw exception to notify the caller
            throw new Exception('GiftCard not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Log general error related to deletion
            Log::error('Error deleting GiftCard: ' . $e->getMessage());

            // Throw generic exception for handling failure
            throw new Exception('An error occurred while deleting the GiftCard.');
        }
    }

    /**
     * Recharge a gift card with a specified amount.
     *
     * This method retrieves a gift card, updates its balance, records the transaction,
     * and optionally sends an email notification to the recipient.
     *
     * @param RechargeGiftCardDTO $dto Data transfer object containing recharge details.
     * @return string Message indicating the recharge status.
     * @throws \Exception If the gift card is not found or the recharge process fails.
     */
    public function handle(RechargeGiftCardDTO $dto): string
    {
        try {
            // Retrieve the gift card by ID or fail if not found
            $giftCard = GiftCard::findOrFail($dto->giftCardId);

            // Update the gift card balance
            $giftCard->amount += $dto->amount;
            $giftCard->save();

            // Log the recharge transaction
            GiftCardRecharge::create($dto->toArray());

            // Determine the recipient for notification
            $recipient = $this->getRecipient($giftCard);

            if ($recipient) {
                // Prepare email data for notification
                $mailData = [
                    'amount' => $dto->amount,
                    'email' => $recipient['email'],
                    'name' => $recipient['name'],
                    'card_no' => $giftCard->card_no,
                    'balance' => $giftCard->amount - $giftCard->expense,
                ];

                // Send notification email
                $result = $this->sendMailAction->sendMail(
                    $mailData,
                    General::class,
                    'Tenant.mail.gift_card_recharge'
                );

                return $result;
            }

            return __('GiftCard recharged successfully.');

        } catch (ModelNotFoundException $e) {
            // Log error when the gift card is not found
            Log::error('GiftCard not found: ' . $e->getMessage());

            // Throw exception to notify the caller
            throw new Exception('GiftCard not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Log general error related to the recharge process
            Log::error('Error RechargeGiftCard: ' . $e->getMessage());

            // Throw generic exception for handling failure
            throw new Exception('An error occurred while recharging the GiftCard.');
        }
    }

    /**
     * Retrieve the recipient details associated with a gift card.
     *
     * Determines whether the recipient is a registered user or customer
     * and returns their details.
     *
     * @param GiftCard $giftCard The gift card instance.
     * @return array|null Recipient information containing name and email, or null if unavailable.
     */
    private function getRecipient(GiftCard $giftCard): ?array
    {
        if ($giftCard->user_id) {
            // Retrieve the user if assigned to the gift card
            $user = User::find($giftCard->user_id);
            return $user ? ['email' => $user->email, 'name' => $user->name] : null;
        }

        if ($giftCard->customer_id) {
            // Retrieve the customer if assigned to the gift card
            $customer = Customer::find($giftCard->customer_id);
            return $customer && $customer->email ? ['email' => $customer->email, 'name' => $customer->name] : null;
        }

        return null;
    }
}
