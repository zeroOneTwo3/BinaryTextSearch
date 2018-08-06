<?php
namespace BinaryTextSearch;

class BinaryTextSearch
{
	const LINE = 'value';
    const POS = 'pos';
	
    protected $buffer_size = 1024;
    protected $lines = null;
    protected $file = null;
	
    protected $first=array();
    protected $last=array();
	
    public function __construct( $filename, $compare=null ) {
        $this->setComparator($compare);
        if( ($this->file = new \SplFileObject( $filename ) ) === false ) {
            $this->errorManager();
        }	
    }
	protected function init() {
		// Set count of lines
		$this->file->seek($this->file->getSize()); //PHP_INT_MAX
        $this->lines = $this->file->key();
		// Set first and last lines
		$this->first = $this->getLineAt(0);
		$this->last = $this->getLineAt($this->lines-1);
    }
	
	protected function stringComparator( $a, $b ) {
        return strcmp($this->getKey($a), $b);
    }
    protected function setComparator( $compare=null ) {
        if( $compare == null ) {
            $compare = array($this, 'stringComparator');
        }
        if( is_callable($compare) == false ) {
            throw new \InvalidArgumentException(sprintf('%s is not a callable function', print_r($compare, 1)));
        }
        $this->compare = $compare;
    }
    protected function compare( $line, $value ) {
        return call_user_func($this->compare, $line, $value);
    }
	
	protected function getKey($line){
		return substr($line, 0, strpos($line, "\t"));
	}
	protected function getVal($line){
		return trim(substr($line, strpos($line, "\t")));
	}
	
    public function search( $value ) {
        $this->init();
        $lo = $this->first;
        $hi = $this->last;
        try {
            while($hi[self::POS] > $lo[self::POS] ) {
                $mid = $this->getMid($lo, $hi);
                $cmp = $this->compare( $mid[self::LINE], $value );
                if ($cmp < 0) {
                    $lo = $this->getNext($mid);
                } elseif ($cmp > 0) {
                    $hi = $this->getPrevious($mid);
                } else {
                    return $this->getVal($mid[self::LINE]);
                }
            }
        } catch( \InvalidArgumentException $ex ) {}
        return $this->compare( $lo[self::LINE], $value ) == 0 ? $this->getVal($lo[self::LINE]) : null;
    }
	
    protected function getMid( array $lo, array $hi ) {
        $pos = $lo[self::POS] + round(($hi[self::POS]-$lo[self::POS])/2);
        return $this->getLineAt($pos);
    }
    protected function getNext( array $mid ) {
        $pos = $mid[self::POS]+1;
        return $this->getLineAt($pos);
    }
    protected function getPrevious( array $mid ) {
        return $this->getLineAt($mid[self::POS]-1);
    }
	
    protected function getLineAt( $pos ) {
        $this->fseek( $pos );
        return array(
            self::LINE => $this->file->fgets(),
            self::POS => $pos
        );
    }
    protected function fseek($offset=0) {
        if( $offset < 0 || $offset > $this->lines) {
            throw new \InvalidArgumentException(sprintf('offset %d is not valid', $offset));
        }
		$this->file->seek($offset);
    }
    protected function errorManager( $message=null ) {
        if(( $error = error_get_last()) != null ) {
            $pattern = $message != null ? "\n%s" : '%s';
            $message .= sprintf($pattern, $error['message']);
        }
        throw new \ErrorException( $message, $error['type'], 1, $error['file'], $error['line'] );
    }
}