<?php
declare(strict_types = 1);
namespace Spec\EncryptionHelper;

use EncryptionHelper\EncryptionHelper;
use PhpSpec\ObjectBehavior;

//use Prophecy\Argument;
/**
 * Class EncryptionHelperSpec
 *
 * @mixin \EncryptionHelper\EncryptionHelper
 *
 * @method void during($method, array $params)
 * @method void shouldBe($value)
 * @method void shouldContain($value)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 */
class EncryptionHelperSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(EncryptionHelper::class);
    }
    public function it_should_be_possible_to_add_new_queries_to_list()
    {
        $this->hasQuery('Stars')
             ->shouldReturn(false);
        $this->addQuery('Stars', '5');
        $this->hasQuery('Stars')
             ->shouldReturn(true);
    }
    public function it_should_be_possible_to_remove_existing_queries_from_list()
    {
        $this->addQuery('Stars', '5');
        $this->hasQuery('Stars')
             ->shouldReturn(true);
        $this->removeQuery('Stars')
             ->shouldReturn(true);
        $this->hasQuery('Stars')
             ->shouldReturn(false);
    }
    public function it_should_clear_query_list_in_process_query_string_when_data_has_bad_checksum()
    {
        $this->hasQuery('Rating')
             ->shouldReturn(true);
        $given = 'Rating=80&__$$=26F';
        $this->processQueryString($given);
        $this->hasQuery('Rating')
             ->shouldReturn(false);
        $given = 'Stars=5&__$$=14D';
        $this->processQueryString($given);
        $this->hasQuery('Stars')
             ->shouldReturn(false);
    }
    public function it_should_clear_query_list_in_process_query_string_when_data_is_empty()
    {
        $this->hasQuery('Rating')
             ->shouldReturn(true);
        $this->processQueryString('');
        $this->hasQuery('Rating')
             ->shouldReturn(false);
    }
    public function it_should_clear_query_list_in_process_query_string_when_data_is_missing_checksum()
    {
        $this->hasQuery('Rating')
             ->shouldReturn(true);
        $given = 'Rating=80';
        $this->processQueryString($given);
        $this->hasQuery('Rating')
             ->shouldReturn(false);
        $given = 'Stars=5';
        $this->processQueryString($given);
        $this->hasQuery('Stars')
             ->shouldReturn(false);
//        $expected = 'Rating=80&__$$=14D';
//        $expected = 'Rating=80&Stars=5&__$$=26F';
//        $this->__toString()
//             ->shouldReturn($expected);
    }
    public function it_should_never_add_checksum_to_query_list_in_process_query_string()
    {
        $this->hasQuery('__$$')
             ->shouldReturn(false);
        $given = 'Rating=80&__$$=14D';
        $this->processQueryString($given);
        $this->hasQuery('__$$')
             ->shouldReturn(false);
        $this->hasQuery('Stars')
             ->shouldReturn(false);
        $given = 'Rating=80&Stars=5&__$$=26F';
        $this->processQueryString($given);
        $this->hasQuery('__$$')
             ->shouldReturn(false);
        $this->hasQuery('Stars')
             ->shouldReturn(true);
    }
    public function it_should_return_cipher_text_from_encrypt()
    {
        $given = 'Rating=80&__$$=14D';
        $this->encrypt($given)
             ->shouldReturn($this->encryptedData);
        $given = 'Stars=5';
        $expected = '8A92415A14CD52A5';
        $this->encrypt($given)
             ->shouldReturn($expected);
    }
    public function it_should_return_empty_string_from_decrypt_when_data_is_empty()
    {
        $this->decrypt('')
             ->shouldReturn('');
    }
    public function it_should_return_false_from_has_query_when_name_does_not_exists()
    {
        $this->hasQuery('Stars')
             ->shouldReturn(false);
    }
    public function it_should_return_false_from_remove_query_when_name_does_not_exists()
    {
        $this->removeQuery('Stars')
             ->shouldReturn(false);
    }
    public function it_should_return_query_list_from_decrypt_when_data_is_good()
    {
        $expected = 'Rating=80&__$$=14D';
        $this->decrypt($this->encryptedData)
             ->shouldReturn($expected);
        $expected = 'Stars=5';
        $given = '8A92415A14CD52A5';
        $this->decrypt($given)
             ->shouldReturn($expected);
    }
    public function it_should_return_query_list_with_checksum_from_to_string()
    {
        $expected = 'Rating=80&__$$=14D';
        /** @noinspection ImplicitMagicMethodCallInspection */
        $this->__toString()
             ->shouldReturn($expected);
    }
    public function it_should_return_true_from_has_query_when_name_exists()
    {
        $this->addQuery('Stars', '5');
        $this->hasQuery('Stars')
             ->shouldReturn(true);
    }
    public function it_should_return_true_from_remove_query_when_name_exists()
    {
        $this->addQuery('Stars', '5');
        $this->removeQuery('Stars')
             ->shouldReturn(true);
    }
    public function it_throws_exception_in_add_query_when_empty_name_is_given()
    {
        $message = $message = 'Query name can not be empty';
        $this->shouldThrow(new \OutOfBoundsException($message))
             ->during('addQuery', ['', 'test']);
    }
    public function it_throws_exception_in_set_cipher_when_given_unknown_cipher()
    {
        $given = 'IDoNotExist';
        $message = sprintf('Cipher %s is not known', $given);
        $this->shouldThrow(new \RangeException($message))
             ->during('setCipher', [$given]);
    }
    public function it_throws_exception_in_set_init_vector_when_iv_is_to_short()
    {
        $given = 'a';
        $message = sprintf('Initialization vector must be at least %s characters long', 8);
        $this->shouldThrow(new \RangeException($message))
             ->during('setInitVector', [$given]);
    }
    public function it_throws_exception_in_set_key_when_key_is_to_short()
    {
        $given = 'a';
        $message = sprintf('Key must be at least %s characters long', 8);
        $this->shouldThrow(new \RangeException($message))
             ->during('setKey', [$given]);
    }
    public function let()
    {
        $this->beConstructedWith($this->encryptedData);
    }
    protected $encryptedData = 'F7EBC908B106D4282FA705D0EED915DBE002774B1A152DCC';
}
