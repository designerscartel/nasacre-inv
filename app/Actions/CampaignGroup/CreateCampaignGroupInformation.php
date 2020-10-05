<?php

namespace App\Actions\CampaignGroup;

use App\Models\CampaignGroup;
use App\Models\CampaignEmail;
use App\Models\Sacre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\CampaignGroup\CreatesCampaignGroupInformation;

class CreateCampaignGroupInformation implements CreatesCampaignGroupInformation
{
    /**
     * Validate and Create the given Campaign's information.
     *
     * @param array $input
     * @return void
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createCampaignGroupInformation');

        $campaignGroup = new CampaignGroup();
        $campaignGroup->title = $input['title'];
        $campaignGroup->save();

        if ((isset($input['members'])) || (isset($input['nonmembers'])) || (isset($input['chair'])) || (isset($input['clerk'])) || (isset($input['la'])) || (isset($input['adviser']))) {

            $sacres = Sacre::all();
            $sacreEmails = [];

            foreach ($sacres as $sacre) {

                // only include if the sacre is a member
                if (isset($input['members'])) {
                    if (empty($sacre->member)) {
                        continue;
                    }
                }

                // only include if the sacre is a none member
                if (isset($input['nonemembers'])) {
                    if (!empty($sacre->member)) {
                        continue;
                    }
                }

                foreach ($sacre->contacts as $contact) {

                    if ($contact->position_id === 2) {
                        if (!empty($contact->email)) {
                            if (isset($input['clerk'])) {
                                $sacreEmails[] = [
                                    'campaign_group_id' => $campaignGroup->id,
                                    'name' => $contact->name,
                                    'email' => $contact->email
                                ];
                            }
                        }
                    } elseif ($contact->position_id === 3) {
                        if (!empty($contact->email)) {
                            if (isset($input['la'])) {
                                $sacreEmails[] = [
                                    'campaign_group_id' => $campaignGroup->id,
                                    'name' => $contact->name,
                                    'email' => $contact->email
                                ];
                            }
                        }
                    } elseif ($contact->position_id === 4) {
                        if (!empty($contact->email)) {
                            if (isset($input['adviser'])) {
                                $sacreEmails[] = [
                                    'campaign_group_id' => $campaignGroup->id,
                                    'name' => $contact->name,
                                    'email' => $contact->email
                                ];
                            }
                        }
                    } elseif ($contact->position_id === 1) {
                        if (!empty($contact->email)) {
                            if (isset($input['chair'])) {
                                $sacreEmails[] = [
                                    'campaign_group_id' => $campaignGroup->id,
                                    'name' => $contact->name,
                                    'email' => $contact->email
                                ];
                            }
                        }
                    }
                }

            }
            if(!empty($sacreEmails)) {
                CampaignEmail::insert($sacreEmails);
            }
        }


    }
}
