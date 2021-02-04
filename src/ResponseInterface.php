<?php
/**
 * The copyright in all works of authorship created pursuant to this agreement
 * are owned by Technofox India Pvt Ltd. All such works or portions of works
 * created by Developer are "works made for hire" as defined in 17 U.S.C. § 201.
 * Developer assigns to Technofox all right, title, and interest in:
 *  (a) The copyright to all works of authorship ("Work") and contribution to
 *      any such Work ("Contribution") created pursuant to this agreement;
 *  (b) Any registrations and copyright applications, along with any renewals
 *      and extensions thereof, relating to the Contribution or the Work;
 *  (c) All works based upon, derived from, or incorporating the Contribution
 *      or the Work;
 *  (d) All income, royalties, damages, claims and payments now or hereafter
 *      due or payable with respect to the Contribution or the Work;
 *  (e) All causes of action, either in law or in equity, for past, present,
 *      or future infringement of copyright related to the Contribution or the Work,
 *      and all rights corresponding to any of the foregoing, throughout the world.
 *      Developer may use the Work only until Developer delivers a final product to
 *      Technofox, and may use the Work only insomuch as such use is necessary to the
 *      creation of the final product. Technofox grants no license to developer for any
 *      use of the Work other than as expressly described herein.
 *
 * Developer must request a separate license from Technofox for any use of the Work
 * other than as expressly described herein. Such license must be explicitly granted
 * in writing, signed by Technofox, or it is void. Should a court of law with
 * jurisdiction over the parties and the subject matter of this contract deem the Work
 * not a "work for hire," and should a court of law with jurisdiction over the parties
 * and the subject matter judge the above assignment of copyright void, Developer grants
 * Technofox an exclusive,royalty-free, irrevocable worldwide license to use the Work without
 * limitation in any manner Technofox deems appropriate.
 *
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 02/02/2021
 * Time: 23:10
 */

namespace yii\swiftsmser;

interface ResponseInterface
{
    public function getDecoded(): object;
    public function getRaw(): string;
    public function getResponseId(): string;
    public function setResponseId(string $resp_id): self;
    public function setRaw(string $raw): self;
    public function setStatus(string $status): self;
    public function getStatus(): string;
}
